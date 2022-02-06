<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GuestTribeResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "cover" => $this->cover,
            "description" => $this->description,
            "type" => $this->type,
            "amount" => $this->amount,
            "users" => count($this->users),
            "discussions" => $this->discussions?count($this->discussions):0,
            'category' => $this->category,
            'tags' => $this->tags
        ];
    }
}
