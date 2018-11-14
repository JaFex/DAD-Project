<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Item extends JsonResource
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
            'name' => $this->name,
            'shortDescription' => substr($this->description,0,49).'...',
            'longDescription' => $this->description,
            'price' => $this->price,
            'type' => $this->type,
            'photoUrl' => 'storage/items/' . $this->photo_url
        ];
    }
}
