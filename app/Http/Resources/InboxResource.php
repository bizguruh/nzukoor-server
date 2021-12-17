<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Admin;
use App\Models\Facilitator;
use App\Http\Resources\UserResource;
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
            'user_id' => $this->user_id,
            'user' => new UserNameResource($this->user),
            'receiver_id' => $this->receiver_id,
            'receiver' => $this->receiver,
            'receiver_info' =>  new UserNameResource(User::find($this->receiver_id)),
            'created_at' => $this->created_at,
            'is_read' => $this->is_read
        ];
    }
}
