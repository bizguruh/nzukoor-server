<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeedCommentResource extends JsonResource
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
            "comment" =>  $this['comment'],
            "commentreplycount" => count($this['feedcommentreplies']),
            'feedcommentreplies' =>  FeedCommentRepliesResource::collection($this['feedcommentreplies']),
            // "feedcommentlikes" => $this['feedcommentlikes'],
            "isLiked" => $this['feedcommentlikes'] ? true : false,
            "feed_id" => $this['feed_id'],
             "user_id" => $this['user_id'],
             'user' => new UserNameResource($this['user'])
        ];
    }
}
