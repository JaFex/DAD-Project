<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\User;

class Meal extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $waiter = User::withTrashed()->findOrFail($this->responsible_waiter_id);
        return [
            'id' => $this->id,
            'state' => $this->state,
            'table_number' => $this->table_number,
            'start' => $this->start,
            'end' => $this->end,
            'total_price_preview' => $this->total_price_preview,
            'responsible_waiter_id' => $waiter->id,
            'responsible_waiter_name' => $waiter->name
        ];
    }
}
