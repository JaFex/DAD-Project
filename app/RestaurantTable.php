<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class RestaurantTable extends Model
{

    use SoftDeletes;

    protected $fillable = ['table_number'];
    protected $guarded = ['deleted_at', 'created_at', 'updated_at'];
}
