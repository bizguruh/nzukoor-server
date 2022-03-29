<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function isAttending($arr){
       $userIds = collect($arr)->map(function($a){
            return $a->user_id;
        })->toArray();
        $user = auth('api')->user();
        if(is_null($user)) return false;
        return in_array($user->id, $userIds);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'venue' => $this->venue,
            'description' => $this->description,
            'schedule' => $this->duration,
            'facilitators' =>[],
            'url' => $this->url,
            'start' => $this->start,
            'end' => $this->end,
            'status' => $this->status,
            'resource' => $this['resource'],
            'attendance' => count($this->eventattendance),
            'is_attending' => $this->isAttending($this->eventattendance),
            'cover' => $this->cover,
            'tribe_id' => $this->tribe_id,
            'tribe' => $this->tribe,
            "user" => new UserNameResource($this->user)

        ];
    }
}
