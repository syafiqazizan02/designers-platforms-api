<?php
namespace App\Repositories\Eloquent;


use App\Models\Chat;
use App\Repositories\Contracts\IChat;

class ChatRepository extends BaseRepository implements IChat
{

    public function model()
    {
        return Chat::class; // chat table
    }

    public function createParticipants($chatId, array $data)
    {
        $chat = $this->model->find($chatId);
        $chat->participants()->sync($data); // create to recipient table
    }

    public function getUserChats()
    {
        return auth()->user()->chats() // user auth
            ->with(['messages', 'participants']) // message of participants
            ->get();
    }
}
