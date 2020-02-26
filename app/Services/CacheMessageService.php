<?php

namespace App\Services;

use App\Message;
use Illuminate\Support\Facades\Redis;

class CacheMessageService
{
    /**
     * @return array
     */
    static public function getNew(): array
    {
        $messages = [];
        if ( Redis::connection('cache')->exists('trigger_session_'.session()->getId()) ) {
                Redis::connection('cache')->del('trigger_session_'.session()->getId());
                while (true) {
                    $message = Redis::connection('cache')->rpop('session_messages_'.session()->getId());
                    if ($message) $messages[] = json_decode($message);
                    else break;
                }
            }
        return $messages;
    }

    /**
     * @param array $data
     * @return Message
     */
    static public function store(array $data): Message
    {
        $message_id = Message::create($data)->id;
        $message = Message::with('user:id,email')->findOrFail($message_id);
        $cache = Redis::connection('cache');
        foreach (Redis::connection('session')->command('keys', ['*']) as $key) {
            $session_id = substr($key, strpos($key, ":") + 1);
            if ($session_id !== session()->getId()) {
                $cache->lpush('session_messages_' . $session_id, json_encode($message));
                $cache->expire('session_messages_' . $session_id, 10);
                $cache->set('trigger_session_' . $session_id, true);
                $cache->expire('trigger_session_' . $session_id, 10);
            }
        }
        return $message;
    }
}

