<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
     // participants of chat (group)
     public function participants()
     {
         return $this->belongsToMany(User::class, 'participants'); // bridge participants table
     }

     public function messages()
     {
         return $this->hasMany(Message::class);
     }

     // get latest message
     public function getLatestMessageAttribute()
     {
         return $this->messages()->latest()->first();
     }

     // unread message by user
     public function isUnreadForUser($userId)
     {
         return (bool)$this->messages()
                 ->whereNull('last_read')
                 ->where('user_id', '<>', $userId)
                 ->count();
     }

     // mark for read message
     public function markAsReadForUser($userId)
     {
         $this->messages()
             ->whereNull('last_read')
             ->where('user_id', '<>', $userId)
             ->update([
                 'last_read' => Carbon::now()
             ]);
     }
}
