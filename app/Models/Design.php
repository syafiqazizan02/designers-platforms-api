<?php

namespace App\Models;

use App\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentTaggable\Taggable;

class Design extends Model
{
    use Taggable, Likeable;

    protected $fillable=[
        'user_id',
        'team_id',
        'image',
        'title',
        'description',
        'slug',
        'close_to_comment',
        'is_live',
        'upload_successful',
        'disk'

    ];

    protected $casts=[
        'is_live' => 'boolean',
        'upload_successful' => 'boolean',
        'close_to_comments' => 'boolean'
    ];

    // one user one design
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // many comments for designs
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')
                ->orderBy('created_at', 'asc');
    }

    // one design one team
    public function team(){
        return $this->belongsTo(Team::class);
    }

    public function getImagesAttribute() // image slugs
    {
        return [
            'thumbnail' => $this->getImagePath('thumbnail'),
            'large' => $this->getImagePath('large'),
            'original' => $this->getImagePath('original'),
        ];
    }

    protected function getImagePath($size) // override getImagePaths to getImagesAttribute
    {
        return Storage::disk($this->disk)
                        ->url("uploads/designs/{$size}/".$this->image);
    }
}
