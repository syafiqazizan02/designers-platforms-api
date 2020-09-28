<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $touches=['chat']; // auto update datetime (watching actions)

    protected $fillable=[
        'user_id',
        'chat_id',
        'body',
        'last_read'
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id'); // link by user_id
    }
}
