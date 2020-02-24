<?php

namespace App\Repositories;

use App\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;

class MessageRepository
{
    public static function getMessagesCreatedAfterDate(): Collection
    {
        return Message::where('created_at', '>=', auth()->user()->last_login_date)
            ->with('user:id,email')
            ->get();
    }
}
