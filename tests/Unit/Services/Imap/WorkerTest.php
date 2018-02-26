<?php

namespace Tests\Unit\Services\Imap;

use App\Contracts\Services\Imap\Client;
use App\Events\Imap\ImapConnectionFailed;
use App\Events\Imap\MessageFailed;
use App\Events\Imap\MessageProcessed;
use App\Events\Imap\MessageProcessing;
use App\Services\Imap\Worker;
use App\Services\Imap\WorkerOptions;
use Ddeboer\Imap\Exception\InvalidResourceException;
use Ddeboer\Imap\MessageInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Mockery as m;

class WorkerTest extends TestCase
{
    function test_a_email_message_success_processed()
    {
        Event::fake();

        $worker = $this->getWorker();

        $message = m::mock(MessageInterface::class);
        $message->shouldReceive('markAsSeen')->once();

        $worker->handleMessage(
            $message,
            m::mock(WorkerOptions::class)
        );

        Event::assertDispatched(MessageProcessing::class, function ($e) use ($message) {
            return $e->message == $message;
        });

        Event::assertDispatched(MessageProcessed::class, function ($e) use ($message) {
            return $e->message == $message;
        });
    }

    function test_handle_message_with_exception()
    {
        $this->app->instance(
            ExceptionHandler::class,
            $handler = m::mock(ExceptionHandler::class)
        );

        $exception = new \Exception('Process failed');

        $handler->shouldReceive('report')->with($exception);

        $worker = $this->getWorker();
        $message = m::mock(MessageInterface::class);

        $this->assertFalse($worker->shouldQuit);

        $message->shouldReceive('markAsSeen')->once()->andReturnUsing(function () use($exception) {
            throw $exception;
        });

        $worker->handleMessage(
            $message,
            m::mock(WorkerOptions::class)
        );
    }

    function test_a_email_message_fail_processed_with_thrown_an_exception()
    {
        Event::fake();

        $worker = $this->getWorker();

        $message = m::mock(MessageInterface::class);
        $message->shouldReceive('markAsSeen')->once()->andReturnUsing(function () {
            throw new \Exception('Process failed');
        });

        try {
            $worker->process(
                $message,
                m::mock(WorkerOptions::class)
            );
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
        }

        Event::assertDispatched(MessageProcessing::class, function ($e) use ($message) {
            return $e->message == $message;
        });

        Event::assertDispatched(MessageFailed::class, function ($e) use ($message) {
            return $e->message == $message;
        });

        Event::assertNotDispatched(MessageProcessed::class);
    }

    function test_check_success_imap_connection()
    {
        Event::fake();

        $this->app->instance(
            Client::class,
            $client = m::mock(Client::class)
        );

        $client->shouldReceive('ping')->once()->andReturnTrue();

        $worker = $this->getWorker();

        $this->assertTrue(
            $worker->checkImapServerConnection(m::mock(WorkerOptions::class))
        );
    }

    function test_check_failed_imap_connection()
    {
        Event::fake();

        $this->app->instance(
            Client::class,
            $client = m::mock(Client::class)
        );

        $exception = new InvalidResourceException('Message');

        $client->shouldReceive('ping')->once()->andReturnUsing(function() use($exception) {
            throw $exception;
        });

        $worker = $this->getWorker();

        $this->assertFalse(
            $worker->checkImapServerConnection(m::mock(WorkerOptions::class))
        );

        Event::assertDispatched(ImapConnectionFailed::class, function ($e) use ($exception) {
            return $e->exception == $exception;
        });
    }

    protected function getWorker(): Worker
    {
        return new Worker(
            $this->app[Dispatcher::class],
            $this->app[Client::class],
            $this->app[ExceptionHandler::class]
        );
    }
}
