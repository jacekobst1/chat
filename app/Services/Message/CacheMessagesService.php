<?php


namespace App\Services\Message;


use App\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CacheMessagesService
{
    static public function getNewAfterId(int $id): array
    {
        $messages = [];
        if (Cache::get('last_message_id') !== $id) {
            for ($i = $id+1; $i <= Cache::get('last_message_id'); $i++) {
                $messages[] = Cache::get('message_'.$i);
            }
        }
        return $messages;
    }

    static public function store(array $data): Message
    {
        $message_id = Message::create($data)->id;
        $message = Message::with('user:id,email')->findOrFail($message_id);
        Cache::forever('last_message_id', $message_id);
        Cache::put('message_'.$message_id, $message, 5);
        return $message;
    }
}
