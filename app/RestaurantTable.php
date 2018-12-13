<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $fillable = ['table_number'];
    protected $guarded = ['deleted_at', 'created_at', 'updated_at'];
}
