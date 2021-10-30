<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscussionMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function handleVote()
    {
        $positive = count(array_filter($this->discussionmessagevote->toArray(), function ($a) {
            return $a['vote'];
        }));
        $negative = count(array_filter($this->discussionmessagevote->toArray(), function ($a) {
            return !$a['vote'];
        }));
        return $positive - $negative;
    }
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'attachment' => $this->attachment,
            'message' => $this->message,
            'message' => $this->message,
            'discussionmessagecomment' => $this->discussionmessagecomment,
            'user' => new UserResource($this->user),
            "discussionmessagevote" => $this->handleVote(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
