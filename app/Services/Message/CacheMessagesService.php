<?php


namespace App\Services\Message;


use App\Message;
use Illuminate\Support\Facades\Cache;

class CacheMessagesService
{
    static public function getNewAfterId(int $id): array
    {
        $messages = [];
        if (Cache::get('last_message_id') !== $id) {
            for ($i=$id; $i<=Cache::get('last_message_id'); $i++) {
                $messages[] = Cache::get('message_'.$i);
            }
        }
        return $messages;
    }

    static public function store(array $data): Message
    {
        $message = Message::create($data);
        Cache::forever('last_message_id', $message->id);
        Cache::put('message_'.$message->id, $message, 1440);
        return $message;
    }
}
