<?php

namespace App\Http\Controllers\Chats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IChat;
use App\Repositories\Contracts\IMessage;

class ChatController extends Controller
{
    protected $chats;
    protected $messages;

    public function __construct(IChat $chats, IMessage $messages)
    {
        $this->chats = $chats;
        $this->messages = $messages;
    }

    // Send message to user
    public function sendMessage(Request $request)
    {

    }

    // Get chats for user
    public function getUserChats()
    {

    }

    // get messages for chat
    public function getChatMessages($id)
    {

    }

    // mark chat as read
    public function markAsRead($id)
    {

    }

    // destroy message
    public function destroyMessage($id)
    {

    }
}
