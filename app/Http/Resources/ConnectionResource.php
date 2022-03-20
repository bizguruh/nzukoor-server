<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Inbox;
use Illuminate\Http\Resources\Json\JsonResource;

class ConnectionResource extends JsonResource
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
            'user_follower' =>  new UserResource(User::find($this->following_id)),

        ];
    }
}
