<?php

namespace App\Repositories;

use App\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;

class MessageRepository
{
    public static function getMessagesCreatedAfterDate(): Collection
    {
        return Message::whereDate('created_at', '<=', Date::now())
            ->with('user:id,email')
            ->get();
    }
}
