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
    public function toArray($request)
    {
        return [
            "id" => $this['id'],
            "name" => $this['name'],
            "profile" => $this['profile'],
            "bio" => $this['bio'],
            "username" => $this['username'],
            'interests' => $this['interests'],
            "email" => $this['email']
        ];
    }
}
