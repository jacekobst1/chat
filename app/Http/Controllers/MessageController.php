<?php

namespace App\Http\Controllers;

use App\Repositories\MessageRepository;
use App\Http\Requests\StoreRequest;
use App\Services\CacheMessageService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(MessageRepository $messageRepository)
    {
        $messages = $messageRepository->getMessagesCreatedAfterDate();
        return view('chat', compact('messages'));
    }

    public function getNew()
    {
        $messages = CacheMessageService::getNew();
        return response()->json(['status' => 'ok', 'status_code' => 200, 'messages' => $messages], 200);
    }

    public function store(StoreRequest $request)
    {
        $message = CacheMessageService::store($request->all());
        return response()->json(['status' => 'ok', 'status_code' => 200, 'message_id' => $message->id], 200);
    }


}
