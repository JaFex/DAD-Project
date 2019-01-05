<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        //return parent::toArray($request);

        $meal = User::withTrashed()->findOrFail($this->meal_id);

        return [
            'id' => $this->id,
            'state' => $this->state,
            'item_id' => $this->item_id,
            'meal_id' => $this->meal_id,
            'responsible_cook_id' => $this->responsible_cook_id,
            'start' => $this->start,
            'end' => $this->end,
            'meal_name' => $meal->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
