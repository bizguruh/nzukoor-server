<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatHistoryResource extends JsonResource
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
            "message"=> $this->message,
        "attachment"=> $this->attachment,
        "receiver"=> "user",
        "receiver_info"=> new UserResource(User::find($this->receiver_id)),
        "receiver_id"=> $this->receiver_id,
        "user_id"=> $this->user_id,
        "status"=> $this->status,
        "is_read"=> $this->is_read,

        ];
    }
}
