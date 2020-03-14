<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialPrice extends Model
{
    protected $table = 'special_prices';

    protected $fillable = [
        'id', 'client_id', 'inventory_id', 'price', 'state'
    ];

    public function client(){
        return $this->belongsTo('App\Models\Client');
    }

    public function inventory(){
        return $this->belongsTo('App\Models\Inventory');
    }
}
