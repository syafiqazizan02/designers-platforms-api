<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user), // check/get form user resources (combine user api response to design)
            'title' => $this->title,
            'slug' => $this->slug,
            'images' => $this->images,
            'is_live' => $this->is_live,
            'likes_count' => $this->likes()->count(),
            'description' => $this->description,
            'tag_list' => [
                'tags' => $this->tagArray,
                'normalized' => $this->tagArrayNormalized,
            ],
            'created_at_dates' => [
                'created_at_human' => $this->created_at->diffForHumans(),
                'created_at' => $this->created_at
            ],
            'updated_at_dates' => [
                'updated_at_human' => $this->updated_at->diffForHumans(),
                'updated_at' => $this->updated_at
            ],
            // $this->team ? - check have or not, : null
            'team' => $this->team ? [
                'id' => $this->team->id,
                'name' => $this->team->name,
                'slug' => $this->team->slug
            ] : null,
            'comments_count' => $this->comments()->count(),
            'comments' => CommentResource::collection($this->whenLoaded('comments')), // collection for comments
            'user' => new UserResource($this->whenLoaded('user')) // collection for users
        ];
    }
}
