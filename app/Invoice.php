<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['meal_id', 'state', 'nif', 'name', 'date', 'total_price'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function invoice_items()
    {
        return $this->hasMany('App\Invoice_item');
    }
    public function meal()
    {
        return $this->belongsTo('App\Meal');
    }
}
