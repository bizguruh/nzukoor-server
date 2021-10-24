<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Tribe extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return  [
            'data' => TribeResource::collection($this->collection),
            'pagination' => [
                'current_page' => $this->currentPage(),
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'total_page' => $this->lastPage()
            ]
        ];
    }
}
