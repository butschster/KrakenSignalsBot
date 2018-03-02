<?php

namespace App\Services\Imap;

use App\Contracts\Services\Imap\Client as ClientContract;
use Ddeboer\Imap\ConnectionInterface as Connection;
use Ddeboer\Imap\MessageInterface as Message;
use Ddeboer\Imap\Search\Flag\Unseen;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\ServerInterface as Server;
use Illuminate\Support\Collection;

class Client implements ClientContract
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;


    /**
     * @param Server $server
     * @param string $username
     * @param string $password
     */
    public function __construct(Server $server, string $username, string $password)
    {
        $this->server = $server;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Connect to the imap server
     */
    public function connect(): void
    {
        $this->setConnection(
            $this->server->authenticate($this->username, $this->password)
        );
    }

    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * Ping Imap Server
     *
     * @return bool
     */
    public function ping(): bool
    {
        return $this->connection->ping();
    }

    /**
     * Reconnect to Imap server
     */
    public function reconnect(): void
    {
        $this->connection->close();
        $this->connect();
    }

    /**
     * Get all unread messages
     *
     * @return Collection
     */
    public function getUnreadMessages(): Collection
    {
        $mailbox = $this->connection->getMailbox(ClientContract::MAILBOX_INBOX);

        $search = new SearchExpression();
        $search->addCondition(new Unseen());

        return collect($mailbox->getMessages($search))
            ->map(function (Message $message) {
                return $message;
            });
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function markAsRead(Message $message): bool
    {
        return $message->markAsSeen();
    }

    /**
     * Get a message by message number in given mailbox.
     *
     * @param int $number
     * @param string $mailbox
     * @return Message
     */
    public function getMessage(int $number, string $mailbox): Message
    {
        return $this->getMailBoxOrCreate($mailbox)->getMessage($number);
    }

    /**
     * Move message to Processed mailbox
     *
     * @param Message $message
     */
    public function moveToProcessed(Message $message): void
    {
        $this->moveMessageToMailbox($message, ClientContract::MAILBOX_PROCESSED);
    }

    /**
     * Move message to Failed mailbox
     *
     * @param Message $message
     */
    public function moveToFailed(Message $message): void
    {
        $this->moveMessageToMailbox($message, ClientContract::MAILBOX_FAILED);
    }

    /**
     * @param string $name
     * @return \Ddeboer\Imap\MailboxInterface
     */
    protected function getMailBoxOrCreate(string $name): \Ddeboer\Imap\MailboxInterface
    {
        if (!$this->connection->hasMailbox($name)) {
            return $this->connection->createMailbox($name);
        }

        return $this->connection->getMailbox($name);
    }

    /**
     * @param Message $message
     * @param string $mailbox
     */
    protected function moveMessageToMailbox(Message $message, string $mailbox): void
    {
        try {
            $message->move(
                $this->getMailBoxOrCreate($mailbox)
            );
        } catch (\Exception $e) {}
    }
}