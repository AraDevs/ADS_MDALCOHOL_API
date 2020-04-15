<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';

    protected $fillable = [
        'id', 'purchase_date','payment_type','perception','state'
    ];

    public function purchaseItem(){
        return $this->hasMany('App\Models\PurchaseItem');
    }
}
