<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Models\Facilitator;
use App\Models\User;
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
            'user_follower' => $this->when($this->follow_type === 'user', User::find($this->following_id)),
            'facilitator_follower' => $this->when($this->follow_type === 'facilitator', Facilitator::find($this->following_id)),
            'admin_follower' => $this->when($this->follow_type === 'admin', Admin::find($this->following_id)),
        ];
    }
}
