<?php

namespace App\Http\Resources;

use App\Models\Tribe;
use Illuminate\Http\Resources\Json\JsonResource;

class TribeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // public $user = auth('api')->user()->id;
    public function checkifmember($arr)
    {
        if (!auth('api')->user()) return false;
        $id = auth('api')->user()->id;

        return in_array(
            $id,
            array_map(function ($a) use ($arr) {
                return $a['id'];
            }, $arr->toArray())
        );
    }
    public function isOwner()
    {
        if (!auth('api')->user()) return false;
        $owner_id =   Tribe::find($this->id)->getTribeOwner()->id;
        $id = auth('api')->user()->id;

        return  $owner_id ? $owner_id == $id : false;
    }
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
            "discussions" => $this->discussions ? count($this->discussions) : 0,
            'isMember' => $this->checkifmember($this->users),
            'isOwner' => $this->isOwner(),
            'category' => $this->category,
            'tags' => $this->tags
        ];
    }
}
