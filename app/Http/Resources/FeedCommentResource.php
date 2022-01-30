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
    public function handleisLiked($arr)
    {
        if (!auth('api')->user()) return false;
        $id = auth('api')->user()->id;

        return in_array(
            $id,
            collect($arr)->map(function ($a) {
                return $a['user_id'];
            })->all()
        );
    }
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'created_at' => $this['created_at'],
            "comment" =>  $this['comment'],
            "commentreplycount" => count($this['feedcommentreplies']),
            'feedcommentreplies' =>  FeedCommentRepliesResource::collection($this['feedcommentreplies']),
            'replyCount' => count($this['feedcommentreplies']),
            "isLiked" => $this->handleisLiked($this['feedcommentlikes']),
            "likeCount" => count($this['feedcommentlikes']),
            "feed_id" => $this['feed_id'],
             "user_id" => $this['user_id'],
             'user' => new UserNameResource($this['user'])
        ];
    }
}
