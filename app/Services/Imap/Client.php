<?php

namespace App\Services\Imap;

use App\Contracts\Services\Imap\Client as ClientContract;
use Ddeboer\Imap\Connection;
use Ddeboer\Imap\Message;
use Ddeboer\Imap\Search\Flag\Unseen;
use Ddeboer\Imap\SearchExpression;
use Illuminate\Support\Collection;

class Client implements ClientContract
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get all unread messages
     *
     * @return Collection
     */
    public function getUnreadMessages(): Collection
    {
        $mailbox = $this->connection->getMailbox('INBOX');

        $search = new SearchExpression();
        $search->addCondition(new Unseen());

        return collect($mailbox->getMessages($search))->map(function (Message $message) {
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
}