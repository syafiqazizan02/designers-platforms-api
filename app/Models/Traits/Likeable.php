<?php
namespace App\Models\Traits;

use App\Models\Like;

trait Likeable
{
    public static function bootLikeable()
    {
        static::deleting(function($model){ //check for model
            $model->removeLikes(); // override removeLikes()
        });
    }

    // delete likes when model is being deleted
    // count (is deleted parent design table)
    public function removeLikes()
    {
        if($this->likes()->count()){
            $this->likes()->delete();
        }
    }

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

    public function unlike() //when to unlike designs
    {
        if(! auth()->check()) return;

        // override  isLikedByUser() to check auth
        if(! $this->isLikedByUser(auth()->id())){
            return;
        }

        $this->likes()
            ->where('user_id', auth()
            ->id())->delete(); // delete unlike count
    }

    public function isLikedByUser($user_id) // method for liked by user
    {
        return (bool)$this->likes()
                ->where('user_id', $user_id)
                ->count();  // count like by user_id
    }
}
