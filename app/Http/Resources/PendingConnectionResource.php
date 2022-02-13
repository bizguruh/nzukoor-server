<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class PendingConnectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $id = auth('api')->user()->id;



        $user =  User::find($this->following_id);

        return [
            'id' => $this->id,
            'username' =>  $user->username,
            'name' =>  $user->name,
            'userId' =>  $user->id,
            'bio' => $user->bio,
            'interests' => $user->interests,
            'profile' => $user->profile,
            'email' => $user->email


        ];
    }
}
