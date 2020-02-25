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
        $cache = Redis::connection('cache');
        while (true) {
            if ( $cache->exists('trigger_session_'.session()->getId()) ) {
                $cache->del('trigger_session_'.session()->getId());
                $messages = [];
                while (true) {
                    $message = $cache->rpop('session_messages_'.session()->getId());
                    if ($message) $messages[] = json_decode($message);
                    else break;
                }
                return $messages;
            }
        }
    }

    /**
     * @param array $data
     * @return void
     */
    static public function store(array $data): void
    {
        $message_id = Message::create($data)->id;
        $message = Message::with('user:id,email')->findOrFail($message_id);
        $cache = Redis::connection('cache');
        foreach (Redis::connection('session')->command('keys', ['*']) as $key) {
            $session_id = substr($key, strpos($key, ":") + 1);
            $cache->lpush('session_messages_'.$session_id, json_encode($message));
            $cache->set('trigger_session_'.$session_id, true);
            $cache->expire('trigger_session_'.$session_id, 60);
        }
    }
}

