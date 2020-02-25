<?php

namespace App\Services;

use App\Message;
use Illuminate\Support\Facades\Cache;

class CacheMessageService
{
    static public function getNewAfterId(?int $id): array
    {
        $messages = [];
        if ($id === null) {
            $id = session()->get('init_message_id');
        }
        if (
            Cache::get('last_message_id') !== $id
            && strtotime(Cache::get('last_message_date')) >= strtotime(auth()->user()->last_login_at)
        ) {
            $last_message_id = Cache::get('last_message_id') ?: 0;
            for ($i = $id+1; $i <= $last_message_id; $i++) {
                $message = Cache::get('message_' . $i);
                if ($message) {
                    $messages[] = $message;
                }
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
}
