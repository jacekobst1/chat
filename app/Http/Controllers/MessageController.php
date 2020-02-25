<?php

namespace App\Http\Controllers;

use App\Repositories\MessageRepository;
use App\Http\Requests\StoreRequest;
use App\Services\CacheMessageService;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(MessageRepository $messageRepository)
    {
        $messages = $messageRepository->getMessagesCreatedAfterLoginDate();
        return view('chat', compact('messages'));
    }

    public function getNew()
    {
        $messages = CacheMessageService::getNew();
        return response()->json(['status' => 'ok', 'status_code' => 200, 'messages' => $messages], 200);
    }

    public function store(StoreRequest $request)
    {
        CacheMessageService::store($request->all());
        return response()->json(['status' => 'ok', 'status_code' => 200], 200);
    }


}
