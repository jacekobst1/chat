<?php

namespace App\Http\Controllers;

use App\Message;
use App\Repositories\MessageRepository;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Requests\Message\GetNewRequest;
use App\Services\Message\CacheMessagesService;

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

    public function getNew(GetNewRequest $request)
    {
        $messages = CacheMessagesService::getNewAfterId($request->last_message_id);
        return response()->json(['status' => 'ok', 'status_code' => 200, 'messages' => $messages], 200);
    }

    public function store(StoreRequest $request)
    {
        $message = CacheMessagesService::store($request->all());
        return response()->json(['status' => 'ok', 'status_code' => 200, 'message' => $message], 200);
    }
}
