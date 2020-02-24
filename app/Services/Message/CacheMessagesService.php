<?php

namespace App\Services\Message;

use App\Message;
use Illuminate\Support\Facades\Cache;

class CacheMessagesService
{
    static public function getNewAfterId(?int $id): array
    {
        $messages = [];
        if (!$id) {
            $id = self::getInitId();
        }
        if (
            strtotime(Cache::get('last_message_date')) >= strtotime(auth()->user()->last_login_at)
            && Cache::get('last_message_id') !== $id
        ) {
            for ($i = $id + 1; $i <= Cache::get('last_message_id'); $i++) {
                $messages[] = Cache::get('message_' . $i);
            }
        }
        return $messages;
    }

    static public function store(array $data): Message
    {
        $message_id = Message::create($data)->id;
        $message = Message::with('user:id,email')->findOrFail($message_id);
        Cache::forever('last_message_id', $message_id);
        Cache::forever('last_message_date', $message->created_at);
        Cache::put('message_'.$message_id, $message, 5);
        return $message;
    }

    static private function getInitId()
    {
        $id = Cache::get('init_message_of_user_'.auth()->id());
        if (!$id) {
            $initMessage = Message::orderBy('id', 'DESC')->first();
            $id = $initMessage ? $initMessage->id : -1;
            Cache::put('init_message_of_user_'.auth()->id(), $id, 3);
        }
        return $id;
    }
}
