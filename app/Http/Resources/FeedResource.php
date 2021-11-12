<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function handleIsOwner()
    {
        if (!auth('api')->user()) return false;
        $id = auth('api')->user()->id;
        if ($this['user_id'] == $id) return true;
        return false;
    }
    public function handleIsLiked($arr)
    {
        if (!auth('api')->user()) return false;
        $id = auth('api')->user()->id;

        return in_array(
            $id,
            array_map(function ($a) use ($arr) {
                return $a['user_id'] && $a['like'];
            }, $arr)
        );
    }
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'created_at' => $this['created_at'],
            'message' => $this['message'],
            'media' => $this['media'],
            'commentCount' => count($this['comments']),
            'likesCount' => count($this['likes']),
            'comments' =>  FeedCommentResource::collection($this['comments']),
            'likes' =>  FeedLikeResource::collection($this['likes']),
            'isOwner' => $this->handleIsOwner(),
            'isLiked' => $this->handleIsLiked($this['likes']),
            'url' => $this['url'],
            'publicId' => $this['publicId'],
            'user_id' => $this['user_id'],
            'user' =>  new UserResource($this['user']),
            'tags' => $this['tags'],
            'tribe_id' => $this['tribe_id'],

        ];
    }
}