<?php

namespace App\Http\Resources;

use App\Models\Tribe;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscussionResource extends JsonResource
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
    public function checkifmember($arr)
    {

        if (!auth('api')->user()) return false;
        $id = auth('api')->user()->id;

        return in_array(
            $id,
            array_map(function ($a) use ($arr) {
                return $a;
            }, $arr->toArray())
        );
    }
    public function toArray($request)
    {


        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "type" => $this->type,
            'commentCount' => count($this->discussionmessage),
            "discussionmessage" => DiscussionMessageResource::collection($this->discussionmessage),
            "discussionvote" => $this->handleVote(),
            'discussionview' => $this->discussionview ? $this->discussionview->view : 0,
            'user_id' => $this->user_id,
            "user" => new UserNameResource($this->user),
            'tags' => $this->tags,
            'isMember' => $this->checkifmember(Tribe::find($this->tribe_id)->users()->pluck('user_id')),
            'tribe_id' => $this->tribe_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
