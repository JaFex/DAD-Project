<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Meal;
use App\User;

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
        if(!$waiter) {
            $waiter = User::withTrashed()->findOrFail($meal->responsible_waiter_id);
        }
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
