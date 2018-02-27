<?php

namespace App\Services\Imap;

use App\Contracts\Services\Imap\Client;
use App\Events\Imap\ExceptionOccurred;
use App\Events\Imap\ImapConnectionFailed;
use App\Events\Imap\MessageFailed;
use App\Events\Imap\MessageProcessed;
use App\Events\Imap\MessageProcessing;
use App\Events\Imap\WorkerStopping;
use App\Jobs\Imap\HandleIncomingMessage;
use Ddeboer\Imap\MessageInterface as Message;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DetectsLostConnections;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\Collection;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

class Worker
{
    use DetectsLostConnections;

    /**
     * @var Dispatcher
     */
    private $events;

    /**
     * The cache repository implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The queue manager instance.
     *
     * @var Client
     */
    protected $client;

    /**
     * Indicates if the worker should exit.
     *
     * @var bool
     */
    public $shouldQuit = false;

    /**
     * The exception handler instance.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $exceptions;

    /**
     * Indicates if the worker is paused.
     *
     * @var bool
     */
    public $paused = false;

    /**
     * @param Dispatcher $events
     * @param Client $client
     * @param ExceptionHandler $exceptions
     */
    public function __construct(Dispatcher $events, Client $client, ExceptionHandler $exceptions)
    {
        $this->events = $events;
        $this->client = $client;
        $this->exceptions = $exceptions;
    }

    /**
     * Listen to the given imap mailbox in a loop.
     *
     * @param  WorkerOptions $options
     * @return void
     * @throws Throwable
     */
    public function daemon(WorkerOptions $options)
    {
        $lastRestart = $this->getTimestampOfLastRestart();

        while (true) {
            if (!$this->checkImapServerConnection($options)) {
                $this->sleep(5);
                $this->reconnectToImapServer($options);
                continue;
            }

            if (!$this->daemonShouldRun($options)) {
                $this->pauseWorker($options, $lastRestart);
                continue;
            }

            $messages = $this->getUnreadMessages();

            if ($messages->count() > 0) {
                $messages->each(function ($message) use($options) {
                    $this->handleMessage($message, $options);
                });
            } else {
                $this->sleep($options->sleep);
            }

            $this->stopIfNecessary($options, $lastRestart);
        }
    }

    /**
     * @return Collection
     */
    public function getUnreadMessages(): Collection
    {
        return $this->client->getUnreadMessages();
    }

    /**
     * Stop the worker if we have lost connection to a database.
     *
     * @param  \Throwable $e
     * @return void
     */
    protected function stopWorkerIfLostConnection($e)
    {
        if ($this->causedByLostConnection($e)) {
            $this->shouldQuit = true;
        }

//        if ($this->causedByLostConnectionWithImapServer($e)) {
//            $this->shouldQuit = true;
//        }
    }

    /**
     * @return bool
     */
    protected function causedByLostConnectionWithImapServer($e): bool
    {
        try {
            $this->client->ping();

            return false;
        } catch (\Exception $e) {}

        return true;
    }

    /**
     * Process the given message.
     *
     * @param  Message $message
     * @param  WorkerOptions  $options
     * @return void
     */
    public function handleMessage(Message $message, WorkerOptions $options)
    {
        try {
            $this->process($message, $options);
        } catch (Exception $e) {
            $this->exceptions->report($e);

            $this->stopWorkerIfLostConnection($e);
        } catch (Throwable $e) {
            $this->exceptions->report($e = new FatalThrowableError($e));

            $this->stopWorkerIfLostConnection($e);
        }
    }

    /**
     * Process the given message from.
     *
     * @param  Message $message
     * @param  WorkerOptions $options
     * @return void
     *
     * @throws Throwable
     */
    public function process(Message $message, WorkerOptions $options): void
    {
        try {
            $this->raiseMessageProcessingEvent($message);

            $this->client->markAsRead($message);
            dispatch(new HandleIncomingMessage($message));

            $this->raiseMessageProcessedEvent($message);
        } catch (Exception $e) {
            $this->handleMessageException($message, $options, $e);
        } catch (Throwable $e) {
            $this->handleMessageException(
                $message, $options, new FatalThrowableError($e)
            );
        }
    }

    /**
     * Handle an exception that occurred while the job was running.
     *
     * @param  Message $message
     * @param  WorkerOptions $options
     * @param  \Exception $e
     * @return void
     *
     * @throws \Exception
     */
    protected function handleMessageException(Message $message, WorkerOptions $options, $e)
    {
        try {
            $this->failMessageProcessing($message);
        } finally {
            //
        }

        throw $e;
    }

    /**
     * Mark the given job as failed and raise the relevant event.
     *
     * @param  Message $message
     * @return void
     */
    protected function failMessageProcessing(Message $message)
    {
        $this->events->dispatch(new MessageFailed(
            $message
        ));
    }

    /**
     * Raise the before queue job event.
     *
     * @param  Message $message
     * @return void
     */
    protected function raiseMessageProcessingEvent(Message $message)
    {
        $this->events->dispatch(new MessageProcessing(
            $message
        ));
    }

    /**
     * Raise the after queue job event.
     *
     * @param  Message $message
     * @return void
     */
    protected function raiseMessageProcessedEvent(Message $message)
    {
        $this->events->dispatch(new MessageProcessed(
            $message
        ));
    }

    /**
     * Raise the exception occurred queue job event.
     *
     * @param  Message $message
     * @param  Exception $e
     * @return void
     */
    protected function raiseExceptionOccurredJobEvent(Message $message, $e)
    {
        $this->events->dispatch(new ExceptionOccurred(
            $message, $e
        ));
    }

    /**
     * Determine if the queue worker should restart.
     *
     * @param  int|null $lastRestart
     * @return bool
     */
    protected function shouldRestart($lastRestart)
    {
        return $this->getTimestampOfLastRestart() != $lastRestart;
    }

    /**
     * Get the last queue restart timestamp, or null.
     *
     * @return int|null
     */
    protected function getTimestampOfLastRestart()
    {
        if ($this->cache) {
            return $this->cache->get('imap:worker:restart');
        }
    }

    /**
     * Determine if the queue worker should restart.
     *
     * @param  int|null $lastRestart
     * @return bool
     */
    protected function queueShouldRestart($lastRestart)
    {
        return $this->getTimestampOfLastRestart() != $lastRestart;
    }

    /**
     * Set the cache repository implementation.
     *
     * @param  \Illuminate\Contracts\Cache\Repository $cache
     * @return void
     */
    public function setCache(CacheContract $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Determine if the daemon should process on this iteration.
     *
     * @param  WorkerOptions $options
     * @return bool
     */
    protected function daemonShouldRun(WorkerOptions $options)
    {
        return !($this->paused);
    }

    /**
     * Pause the worker for the current loop.
     *
     * @param  WorkerOptions $options
     * @param  int $lastRestart
     * @return void
     */
    protected function pauseWorker(WorkerOptions $options, $lastRestart)
    {
        $this->sleep($options->sleep > 0 ? $options->sleep : 1);

        $this->stopIfNecessary($options, $lastRestart);
    }

    /**
     * Stop the process if necessary.
     *
     * @param  WorkerOptions $options
     * @param  int $lastRestart
     */
    protected function stopIfNecessary(WorkerOptions $options, $lastRestart)
    {
        if ($this->shouldQuit) {
            $this->kill();
        }

        if ($this->memoryExceeded($options->memory)) {
            $this->stop(12);
        } elseif ($this->queueShouldRestart($lastRestart)) {
            $this->stop();
        }
    }

    /**
     * Determine if the memory limit has been exceeded.
     *
     * @param  int $memoryLimit
     * @return bool
     */
    public function memoryExceeded($memoryLimit)
    {
        return (memory_get_usage() / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * Stop listening and bail out of the script.
     *
     * @param  int $status
     * @return void
     */
    public function stop($status = 0)
    {
        $this->events->dispatch(new WorkerStopping);

        exit($status);
    }

    /**
     * Kill the process.
     *
     * @param  int $status
     * @return void
     */
    public function kill($status = 0)
    {
        $this->events->dispatch(new WorkerStopping);

        if (extension_loaded('posix')) {
            posix_kill(getmypid(), SIGKILL);
        }

        exit($status);
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param  int|float $seconds
     * @return void
     */
    public function sleep($seconds)
    {
        if ($seconds < 1) {
            usleep($seconds * 1000000);
        } else {
            sleep($seconds);
        }
    }

    /**
     * @param WorkerOptions $options
     * @return bool
     * @throws Exception
     */
    public function checkImapServerConnection(WorkerOptions $options): bool
    {
        try {
            $this->client->ping();

            return true;
        } catch (\Exception $e) {
            $this->handleImapException($options, $e);
        }

        return false;
    }

    /**
     * Handle an exception that occurred with imap connection.
     *
     * @param  WorkerOptions $options
     * @param  \Exception $e
     * @return void
     *
     * @throws \Exception
     */
    protected function handleImapException(WorkerOptions $options, $e)
    {
        try {
            $this->failImapConnection($e);
        } catch (\Throwable $e) {
            $this->exceptions->report($e);
        }
    }

    /**
     * Mark the given job as failed and raise the relevant event.
     *
     * @param Exception $e
     * @return void
     */
    protected function failImapConnection(Exception $e)
    {
        $this->events->dispatch(new ImapConnectionFailed(
            $e
        ));
    }

    /**
     * @param WorkerOptions $options
     * @throws Exception
     */
    protected function reconnectToImapServer(WorkerOptions $options): void
    {
        try {
            $this->client->reconnect();
        } catch (\Exception $e) {
            $this->handleImapException($options, $e);
        }
    }
}