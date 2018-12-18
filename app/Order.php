<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['item_id', 'meal_id', 'state', 'responsible_cook_id', 'start', 'end'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    //many to one

    public function meal()
    {
        return $this->belongsTo('App\Meal');
    }

    public function item()
    {
        return $this->hasOne('App\Item');
    }
}
