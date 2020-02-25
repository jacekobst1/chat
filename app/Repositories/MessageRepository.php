<?php

namespace App\Repositories;

use App\Message;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository
{
    /**
     * @return Collection
     */
    public static function getMessagesCreatedAfterLoginDate(): Collection
    {
        return Message::where('created_at', '>=', auth()->user()->last_login_date)
            ->with('user:id,email')
            ->get();
    }
}
