<?php
namespace App\Models\Traits;

use App\Models\Like;

trait Likeable
{
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable'); // fetch the model class
    }

    public function like() //when to like designs
    {
        // checkthe authenticated
        if(! auth()->check()) return;

        // check if the current user has already liked the model
        if($this->isLikedByUser(auth()->id())){ // override isLikedByUser()
            return;
        };

        $this->likes()->create(['user_id' => auth()->id()]);
    }

    public function isLikedByUser($user_id) // method for liked by user
    {
        return (bool)$this->likes()
                ->where('user_id', $user_id)
                ->count();  // count like by user_id
    }
}
