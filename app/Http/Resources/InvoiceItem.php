<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Item;

class InvoiceItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $item = Item::findOrFail($this->item_id);
        return [
            'invoice_id' => $this->invoice_id,
            'item_id' => $this->item_id,
            'meal_id' => $this->meal_id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'sub_total_price' => $this->sub_total_price,
            'item_name' => $item->name,
            'item_type' => $item->type
        ];
    }
}
