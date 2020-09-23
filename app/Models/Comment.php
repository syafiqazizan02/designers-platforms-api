<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'body',
        'user_id'
    ];

    // one comment by one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // many comments for designs
    public function commentable()
    {
        return $this->morphTo();
    }

}
