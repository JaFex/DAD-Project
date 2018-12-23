<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Item extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'type', 'description', 'price', 'photo_url'];
    protected $guarded = ['id', 'name', 'type', 'description', 'photo_url', 'price', 'deleted_at', 'created_at', 'updated_at'];

    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
