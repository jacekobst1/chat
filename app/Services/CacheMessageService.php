<?php

namespace App\Services;

use App\Message;
use App\Repositories\MessageRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheMessageService
{
    static public function getNew()
    {
        while (true) {
            sleep(0.5);
            if ( Cache::get('trigger_session_'.session()->getId()) ) {
                Cache::forget('trigger_session_'.session()->getId());
                return MessageRepository::getMessagesCreatedAfterDate();
            }
        }
    }

    static public function store(array $data): Message
    {
        $message_id = Message::create($data)->id;
        $message = Message::with('user:id,email')->findOrFail($message_id);

        foreach (Redis::connection('default')->command('keys',['*']) as $key) {
            $session_id = substr($key, strpos($key, ":") + 1);
            Cache::put('trigger_session_'.$session_id, true, 5);
        }

        return $message;
    }
}

