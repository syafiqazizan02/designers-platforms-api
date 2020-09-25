<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'owner_id',
        'slug'
    ];

    public function owner()  // team_owner_info
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members() // team_member_user
    {
        return $this->belongsToMany(User::class)
                ->withTimestamps();
    }

    public function designs() // user has many designs
    {
        return $this->hasMany(Design::class);
    }

    // check team has particular user (check current user is list of members)
    public function hasUser(User $user)
    {
        return $this->members()
                    ->where('user_id', $user->id)
                    ->first() ? true : false;  // if has record is true, if no record is false
    }
}
