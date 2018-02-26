<?php

namespace App\Console\Commands;

use App\Contracts\Services\Imap\Client;
use App\Events\Imap\ImapConnectionFailed;
use App\Services\Imap\WorkerOptions;
use Ddeboer\Imap\Message;
use Illuminate\Contracts\Events\Dispatcher;
use App\Events\Imap\MessageFailed;
use App\Events\Imap\MessageProcessed;
use App\Events\Imap\MessageProcessing;
use App\Services\Imap\Worker;
use Illuminate\Console\Command;

class ListenInboxEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imap:listen
                            {--delay=0 : The number of seconds to delay failed messages}
                            {--force : Force the worker to run even in maintenance mode}
                            {--memory=128 : The memory limit in megabytes}
                            {--sleep=5 : Number of seconds to sleep when no messages is available}
                            {--timeout=60 : The number of seconds a child process can run}
                            {--tries=0 : Number of times to attempt a messages before logging it failed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen imap inbox email messages';

    /**
     * @var Dispatcher
     */
    protected $events;

    public function __construct(Dispatcher $events)
    {
        parent::__construct();

        $this->events = $events;
    }

    /**
     * @param Worker $worker
     * @param Client $client
     * @throws \Throwable
     */
    public function handle(Worker $worker, Client $client)
    {
        try {
            $client->connect();
        } catch (\Ddeboer\Imap\Exception\AuthenticationFailedException $e) {
            $this->error('Imap server: Authentication failed');
            return;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        $this->listenForEvents();

        $worker->setCache(
            $this->laravel['cache']->driver()
        );

        $worker->daemon(
            $this->gatherWorkerOptions()
        );
    }

    /**
     * Gather all of the worker options as a single object.
     *
     * @return WorkerOptions
     */
    protected function gatherWorkerOptions()
    {
        return new WorkerOptions(
            $this->option('delay'), $this->option('memory'),
            $this->option('timeout'), $this->option('sleep'),
            $this->option('tries'), $this->option('force')
        );
    }

    /**
     * Listen for the queue events in order to update the console output.
     *
     * @return void
     */
    protected function listenForEvents()
    {
        $this->events->listen(MessageProcessing::class, function ($event) {
            $this->writeOutput($event->message, 'starting');
        });

        $this->events->listen(MessageProcessed::class, function ($event) {
            $this->writeOutput($event->message, 'success');
        });

        $this->events->listen(ImapConnectionFailed::class, function (ImapConnectionFailed $event) {
            $this->error($event->exception->getMessage());
        });

        $this->events->listen(MessageFailed::class, function ($event) {
            $this->writeOutput($event->message, 'failed');

            $this->logFailedJob($event);
        });
    }

    /**
     * Write the status output .
     *
     * @param  Message $message
     * @param  string $status
     * @return void
     */
    protected function writeOutput(Message $message, $status)
    {
        switch ($status) {
            case 'starting':
                return $this->writeStatus($message, 'Processing', 'comment');
            case 'success':
                return $this->writeStatus($message, 'Processed', 'info');
            case 'failed':
                return $this->writeStatus($message, 'Failed', 'error');
        }
    }

    /**
     * Format the status output.
     *
     * @param  Message $message
     * @param  string  $status
     * @param  string  $type
     * @return void
     */
    protected function writeStatus(Message $message, $status, $type): void
    {
        $this->output->writeln(sprintf(
            "<{$type}>[%s] %s</{$type}> %s",
            now()->format('Y-m-d H:i:s'),
            str_pad("{$status}:", 11), $message->getId()
        ));
    }

    /**
     * Store a failed message event.
     *
     * @param  MessageFailed  $event
     * @return void
     */
    protected function logFailedJob(MessageFailed $event)
    {
        // TODO
    }
}
