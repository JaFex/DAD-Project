<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice_item extends Model
{
    protected $fillable = ['invoice_id', 'item_id', 'quantity', 'unit_price', 'sub_total_price'];
    protected $guarded = [];

    public $timestamps = false;

    public function invoice()
    {
        return $this->belongsTo('App\Invoice');
    }

    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
