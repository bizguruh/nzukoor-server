<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function isConnected($id){
         $user = auth('api')->user();
         if(is_null($user)) return false;
          $connections = $user->connections()->get()->map(function($a){return $a->following_id;})->toArray();
         return in_array($id, $connections);
    }
    public function toArray($request)
    {
        return [
            "id" => $this['id'],
            "name" => $this['name'],
            "profile" => $this['profile'],
            "bio" => $this['bio'],
            "username" => $this['username'],
            'interests' => $this['interests'],
            "email" => $this['email'],
            "is_connected"=> $this->isConnected($this['id'])
        ];
    }
}
