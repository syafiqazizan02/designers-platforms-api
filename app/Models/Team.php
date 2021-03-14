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

   // boot function depend on teams to do task on team_member_user
    protected static function boot()
    {
        parent::boot(); // depend on parent model (teams)

        // created, add current user as team member
        static::created(function($team){
            $team->members()->attach(auth()->id());
        });

        // delete on team_user as deleted on teams
        static::deleting(function($team){
            $team->members()->sync([]);
        });
    }

    public function owner() // team_owner_info
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

    public function invitations() // making invitation
    {
        return $this->hasMany(Invitation::class);
    }

    public function hasPendingInvite($email) // pending by email
    {
        return (bool)$this->invitations()
                        ->where('recipient_email', $email)
                        ->count();
    }
}
