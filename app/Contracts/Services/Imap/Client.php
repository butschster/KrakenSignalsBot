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
}