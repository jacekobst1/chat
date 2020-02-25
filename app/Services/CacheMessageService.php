<?php

namespace App\Services;

use App\Message;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheMessageService
{
    /**
     * @return array
     */
    static public function getNew(): array
    {
        while (true) {
            sleep(0.5);
            if ( Cache::get('trigger_session_'.session()->getId()) ) {
                Cache::forget('trigger_session_'.session()->getId());
                $messages = [];
                while (true) {
                    $message = Redis::connection('cache')->rpop('session_messages_'.session()->getId());
                    if ($message) $messages[] = json_decode($message);
                    else break;
                }
                return $messages;
            }
        }
    }

    /**
     * @param array $data
     * @return Message
     */
    static public function store(array $data): Message
    {
        $message_id = Message::create($data)->id;
        $message = Message::with('user:id,email')->findOrFail($message_id);

        foreach (Redis::connection('session')->command('keys',['*']) as $key) {
            $session_id = substr($key, strpos($key, ":") + 1);
            Redis::connection('cache')->lpush('session_messages_'.$session_id, json_encode($message));
            Cache::put('trigger_session_'.$session_id, true, 5);
        }

        return $message;
    }
}

