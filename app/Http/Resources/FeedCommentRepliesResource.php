<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeedCommentRepliesResource extends JsonResource
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
            'id' => $this['id'],
            'created_at' => $this['created_at'],
            "message" =>  $this['message'],
            "feed_comment_id" =>   $this['feed_comment_id'],
            "feedcommentreplylikes" => $this['feedcommentreplylikes'],
            "feed_id" => $this['feed_id'],
            "user_id" => $this['user_id'],
            'user' => new UserResource($this['user'])
        ];
    }
}
