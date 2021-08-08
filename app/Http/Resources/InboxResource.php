<?php

namespace App\Http\Resources;

use App\Models\Admin;
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
            'admin_id' => $this->admin_id,
            'admin' => $this->admin,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'facilitator_id' => $this->facilitator_id,
            'facilitator' => $this->facilitator,
            'receiver_id' => $this->receiver_id,
            'receiver' => $this->receiver,
            'member_info' => $this->when($this->receiver === 'user', User::find($this->receiver_id)),
            'facilitator_info' => $this->when($this->receiver === 'facilitator', Facilitator::find($this->receiver_id)),
            'admin_info' => $this->when($this->receiver === 'admin', Admin::find($this->receiver_id)),
            'created_at' => $this->created_at,
        ];
    }
}
