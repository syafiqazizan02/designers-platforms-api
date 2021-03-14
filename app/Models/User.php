<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable, SpatialTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tagline',
        'about',
        'username',
        'location',
        'formatted_address',
        'available_to_hire'
    ];

    protected $spatialFields = [
        'location',
        'available_to_hire' => 'boolean'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // protect gravatar image
    protected $appends=[
        'photo_url'
    ];

    // get gravatar images
    public function getPhotoUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg';
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // verify email form notification
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    // reset password form notification
    public function sendPasswordResetNotification($token) // getToken to sending back
    {
        $this->notify(new ResetPassword($token));
    }

    // user have many designs
    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    // user have many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

     // teams that the user belongs to
     public function teams()
     {
         return $this->belongsToMany(Team::class)
             ->withTimestamps();
     }

     // teams created that has current member
     public function ownedTeams()
     {
         return $this->teams()
             ->where('owner_id', $this->id);
     }

     // teams owner or lead by
     public function isOwnerOfTeam($team)
     {
         return (bool)$this->teams()
                         ->where('id', $team->id)
                         ->where('owner_id', $this->id)
                         ->count();
     }

     public function invitations()  // relationships for invitations
     {
         return $this->hasMany(Invitation::class, 'recipient_email', 'email');
     }

    // relationships for chat messaging
    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'participants'); // bridge (chats-participants-users)
    }

    // messages by user
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // check user participants for chats (new chat @ availabe chat)
    public function getChatWithUser($user_id)
    {
        $chat = $this->chats()
                    ->whereHas('participants', function($query) use ($user_id){
                        $query->where('user_id', $user_id);
                    })
                    ->first();
        return $chat;
    }


}
