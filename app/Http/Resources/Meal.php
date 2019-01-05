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

        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'state' => $this->state,
            'table_number' => $this->table_number,
            'start' => $this->start,
            'end' => $this->end,
            'responsible_waiter_id' => $this->responsible_waiter_id,
            'responsible_waiter' => $waiter->name,
            'total_price_preview' => $this->total_price_preview,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
