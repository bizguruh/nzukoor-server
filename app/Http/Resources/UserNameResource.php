<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserNameResource extends JsonResource
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
            "username" => $this['username'],
            "email" => $this['email'],
            "bio" => $this['bio']

        ];
    }
}
