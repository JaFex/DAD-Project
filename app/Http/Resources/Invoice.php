<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Meal;

class Invoice extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $meal = Meal::findOrFail($this->meal_id);
        $waiter = $meal->responsible_waiter;
        return [
            'id' => $this->id,
            'state' => $this->state,
            'meal_id' => $this->meal_id,
            'nif' => $this->nif,
            'name' => $this->name,
            'date' => $this->date,
            'total_price' => $this->total_price,
            'waiter_id' => $waiter->id,
            'waiter_name' => $waiter->name,
            'table_number' => $meal->table_number
        ];
    }
}
