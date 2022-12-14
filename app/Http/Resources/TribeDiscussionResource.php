<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TribeDiscussionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function handleVote()
    {
        $positive = count(array_filter($this->discussionvote->toArray(), function ($a) {
            return $a['vote'];
        }));
        $negative = count(array_filter($this->discussionvote->toArray(), function ($a) {
            return !$a['vote'];
        }));
        return $positive - $negative;
    }
    public function toArray($request)
    {


        return [
            "id" => $this->id,
            "name" => $this->name,
            "category" => $this->category,
            "description" => $this->description,
            "type" => $this->type,
            'commentCount' => count($this->discussionmessage),
            "discussionmessage" => $this->discussionmessage,
            "discussionvote" => $this->handleVote(),
            'discussionview' => $this->discussionview ? $this->discussionview->view : 0,
            'user_id' => $this->user_id,
            "user" => new UserNameResource($this->user),
            'tags' => $this->tags,
            'tribe_id' => $this->tribe_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
