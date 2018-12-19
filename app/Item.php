<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [];
    protected $guarded = ['id', 'name', 'type', 'description', 'photo_url', 'price', 'deleted_at', 'created_at', 'updated_at'];

    public function orders()
    {
        return $this->belongsToMany('App\Order');
    }
}
