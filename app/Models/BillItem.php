<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    protected $table = 'bill_items';

    protected $fillable = [
        'id', 'bill_id', 'inventory_id','price','quantity'
    ];

    public function bill(){
        return $this->belongsTo('App\Models\Bill');
    }

    public function inventory(){
        return $this->belongsTo('App\Models\Inventory');
    }
}
