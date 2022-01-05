<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TribeRequestResource extends JsonResource
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
            'id'=>$this->id,
           'user'=> new UserNameResource($this->user),
           'tribe'=>$this->tribe,
           'response'=>$this->response,
            'created_at'=>$this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
