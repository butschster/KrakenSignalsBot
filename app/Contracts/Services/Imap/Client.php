<?php

namespace App\Contracts\Services\Imap;

use Ddeboer\Imap\MessageInterface as Message;
use Illuminate\Support\Collection;

interface Client
{
    const MAILBOX_INBOX = 'INBOX';
    const MAILBOX_PROCESSED = 'Processed';
    const MAILBOX_FAILED = 'Failed';

    /**
     * Get all unread messages
     *
     * @return Collection
     */
    public function getUnreadMessages(): Collection;

    /**
     * Ping Imap Server
     *
     * @return bool
     */
    public function ping(): bool;

    /**
     * Reconnect to Imap server
     */
    public function reconnect(): void;

    /**
     * Connect to the imap server
     */
    public function connect(): void;

    /**
     * Mark email message as read
     * @param Message $message
     * @return bool
     */
    public function markAsRead(Message $message): bool;

    /**
     * Move message to Processed mailbox
     *
     * @param Message $message
     */
    public function moveToProcessed(Message $message): void;

    /**
     * Move message to Failed mailbox
     *
     * @param Message $message
     */
    public function moveToFailed(Message $message): void;

    /**
     * Get a message by message number in given mailbox.
     *
     * @param int $number
     * @param string $mailbox
     * @return Message
     */
    public function getMessage(int $number, string $mailbox): Message;
}