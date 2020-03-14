<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $table = 'production_orders';

    protected $fillable = [
        'id', 'inventory_id', 'quantity', 'start_date', 'end_date', 'exp_date', 'workers', 'hours', 'state'
    ];

    public function inventory(){
        return $this->belongsTo('App\Models\Inventory');
    }
}
