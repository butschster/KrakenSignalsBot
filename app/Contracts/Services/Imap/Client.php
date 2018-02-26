<?php

namespace App\Contracts\Services\Imap;

use Illuminate\Support\Collection;

interface Client
{
    /**
     * Get all unread messages
     *
     * @return Collection
     */
    public function getUnreadMessages(): Collection;

    /**
     * @return bool
     */
    public function ping(): bool;

    public function reconnect(): void;

    /**
     * Connect to the imap server
     */
    public function connect(): void;
}