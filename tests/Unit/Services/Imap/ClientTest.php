<?php

namespace Tests\Unit\Services\Imap;

use App\Services\Imap\Client;
use DateTimeInterface;
use Ddeboer\Imap\ConnectionInterface;
use Ddeboer\Imap\MailboxInterface;
use Ddeboer\Imap\MessageInterface;
use Ddeboer\Imap\MessageIteratorInterface;
use Ddeboer\Imap\Search\ConditionInterface;
use Ddeboer\Imap\ServerInterface;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Mockery as m;

class ClientTest extends TestCase
{
    function test_in_can_be_connected()
    {
        $client = $this->makeClient(
            $server = m::mock(ServerInterface::class)
        );

        $server->shouldReceive('authenticate')
            ->once()
            ->with('test_username', 'test_password')
            ->andReturn(m::mock(ConnectionInterface::class));

        $client->connect();
    }

    function test_ping()
    {
        $client = $this->makeClient();

        $client->setConnection($connection = m::mock(ConnectionInterface::class));

        $connection->shouldReceive('ping')
            ->once()
            ->andReturnTrue();

        $this->assertTrue($client->ping());
    }

    function test_reconnect()
    {
        $client = $this->makeClient(
            $server = m::mock(ServerInterface::class)
        );

        $client->setConnection($connection = m::mock(ConnectionInterface::class));

        $connection->shouldReceive('close')->once();
        $server->shouldReceive('authenticate')
            ->once()
            ->with('test_username', 'test_password')
            ->andReturn($connection);

        $client->reconnect();
    }

    function test_mark_email_message_as_read()
    {
        $client = $this->makeClient();
        $message = m::mock(MessageInterface::class);

        $message->shouldReceive('markAsSeen')->once();

        $client->markAsRead($message);
    }

    function test_gets_unread_messages()
    {
        $client = $this->makeClient();
        $client->setConnection($connection = m::mock(ConnectionInterface::class));

        $connection->shouldReceive('getMailbox')->once()->with('INBOX')->andReturn($mailbox = new TestMailBox);

        $messages = $client->getUnreadMessages();

        $this->assertInstanceOf(Collection::class, $messages);

        foreach ($messages as $message) {
            $this->assertInstanceOf(MessageInterface::class, $message);
        }
    }

    /**
     * @param ServerInterface|null $server
     * @return Client
     */
    protected function makeClient(ServerInterface $server = null): Client
    {
        return new Client(
            $server ?: m::mock(ServerInterface::class),
            'test_username',
            'test_password'
        );
    }
}

class TestMailBox implements MailboxInterface
{

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        // TODO: Implement count() method.
    }

    /**
     * Get mailbox decoded name.
     *
     * @return string
     */
    public function getName(): string
    {
        // TODO: Implement getName() method.
    }

    /**
     * Get mailbox encoded path.
     *
     * @return string
     */
    public function getEncodedName(): string
    {
        // TODO: Implement getEncodedName() method.
    }

    /**
     * Get mailbox encoded full name.
     *
     * @return string
     */
    public function getFullEncodedName(): string
    {
        // TODO: Implement getFullEncodedName() method.
    }

    /**
     * Get mailbox attributes.
     *
     * @return int
     */
    public function getAttributes(): int
    {
        // TODO: Implement getAttributes() method.
    }

    /**
     * Get mailbox delimiter.
     *
     * @return string
     */
    public function getDelimiter(): string
    {
        // TODO: Implement getDelimiter() method.
    }

    /**
     * Get Mailbox status.
     *
     * @param null|int $flags
     *
     * @return \stdClass
     */
    public function getStatus(int $flags = null): \stdClass
    {
        // TODO: Implement getStatus() method.
    }

    /**
     * Bulk Set Flag for Messages.
     *
     * @param string $flag \Seen, \Answered, \Flagged, \Deleted, and \Draft
     * @param array|string $numbers Message numbers
     *
     * @return bool
     */
    public function setFlag(string $flag, $numbers): bool
    {
        // TODO: Implement setFlag() method.
    }

    /**
     * Bulk Clear Flag for Messages.
     *
     * @param string $flag \Seen, \Answered, \Flagged, \Deleted, and \Draft
     * @param array|string $numbers Message numbers
     *
     * @return bool
     */
    public function clearFlag(string $flag, $numbers): bool
    {
        // TODO: Implement clearFlag() method.
    }

    /**
     * Get a message by message number.
     *
     * @param int $number Message number
     *
     * @return MessageInterface
     */
    public function getMessage(int $number): MessageInterface
    {
        // TODO: Implement getMessage() method.
    }

    /**
     * Get messages in this mailbox.
     *
     * @return MessageIteratorInterface
     */
    public function getIterator(): MessageIteratorInterface
    {
        // TODO: Implement getIterator() method.
    }

    /**
     * Add a message to the mailbox.
     *
     * @param string $message
     * @param null|string $options
     * @param null|DateTimeInterface $internalDate
     *
     * @return bool
     */
    public function addMessage(string $message, string $options = null, DateTimeInterface $internalDate = null): bool
    {
        // TODO: Implement addMessage() method.
    }

    /**
     * Returns a tree of threaded message for the current Mailbox.
     *
     * @return array
     */
    public function getThread(): array
    {
        // TODO: Implement getThread() method.
    }

    /**
     * Get message ids.
     *
     * @param ConditionInterface $search Search expression (optional)
     *
     * @return MessageIteratorInterface
     */
    public function getMessages(ConditionInterface $search = null, int $sortCriteria = null, bool $descending = false): MessageIteratorInterface
    {
        return new TestMessageIterator([
            m::mock(MessageInterface::class)
        ]);
    }
}

class TestMessageIterator extends \ArrayIterator implements MessageIteratorInterface
{

    /**
     * Get current message.
     *
     * @return MessageInterface
     */
    public function current(): MessageInterface
    {
        return parent::current();
    }
}