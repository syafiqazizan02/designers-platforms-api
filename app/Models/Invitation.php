<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'recipient_email',
        'sender_id',
        'team_id',
        'token'
    ];

    // which team invitation belongs to
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // get who is get email
    public function recipient()
    {
        return $this->hasOne(User::class, 'email', 'recipient_email'); // only has one
    }

    // get who is send email
    public function sender()
    {
        return $this->hasOne(User::class, 'id', 'sender_id'); // only has one
    }
}
