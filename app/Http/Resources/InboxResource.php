<?php

namespace App\Http\Resources;

use App\Models\Facilitator;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class InboxResource extends JsonResource
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
            'message' => $this->message,
            'attachment' => $this->attachment,
            'status' => $this->status,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'sender' => $this->sender_type == 'user' ? User::find($this->sender_id) : Facilitator::find($this->sender_id),
            'receiver' => $this->receiver_type == 'user' ? User::find($this->receiver_id) : Facilitator::find($this->receiver_id),
            'created_at' => $this->created_at,
        ];
    }
}
