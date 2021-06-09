<?php

namespace App\Http\Resources;

use App\Models\Admin;
use App\Models\Facilitator;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
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
            'learner_detail' => $this->when($this->referree_type === 'learner', User::find($this->referree_id)),
            'facilitator_detail' => $this->when($this->referree_type === 'facilitator', Facilitator::find($this->referree_id)),
            'administrator_detail' => $this->when($this->referree_type === 'administrator', Admin::find($this->referree_id)),
            'created_at' => $this->created_at,
        ];
    }
}
