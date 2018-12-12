<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    //

    public function orders()
    {
        return $this->belongsToMany('App\Order');
    }
}
