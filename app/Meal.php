<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $fillable = ['state', 'table_number', 'responsible_waiter_id', 'total_price_preview', 'start', 'end'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    public function responsible_waiter()
    {
        return $this->belongsTo('App\User');
    }

    public function invoice()
    {
        return $this->hasOne('App\Invoice');
    }
}
