<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    //
    protected $table = 'purchase_items';

    protected $fillable = [
        'id', 'purchase_id', 'inventory_id','price','quantity'
    ];

    public function purchase(){
        return $this->belongsTo('App\Models\Purchase');
    }

    public function inventory(){
        return $this->belongsTo('App\Models\Inventory');
    }
}
