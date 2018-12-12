<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['state', 'responsible_cook_id', 'start', 'end'];
    protected $guarded = ['id', 'item_id', 'meal_id', 'created_at', 'updated_at'];
    //many to one

    public function meal()
    {
        return $this->hasOne('App\Meal');
    }

    public function item()
    {
        return $this->hasOne('App\Item');
    }
}
