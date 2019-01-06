<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;

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
        $cook = null;
        if($this->responsible_cook_id) {
            $cook = User::withTrashed()->findOrFail($this->responsible_cook_id);
        }
        return [
            'id' => $this->id,
            'state' => $this->state,
            'item_id' => $this->item_id,
            'meal_id' => $this->meal_id,
            'responsible_cook_id' => $cook?$cook->id:'',
            'responsible_cook_name' => $cook?$cook->name:'',
            'start' => $this->start,
            'end' => $this->end
        ];
    }
}
