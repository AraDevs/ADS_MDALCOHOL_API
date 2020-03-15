<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventories';
    protected $fillable = [
        'id', 'name', 'description','price','stock','type','state'
    ];

    public function specialPrice(){
        return $this->hasMany('App\Models\SpecialPrice');
    }

    public function rawMaterial(){
        return $this->hasOne('App\Models\RawMaterial');
    }

    public function purchaseItem(){
        return $this->belongsTo('App\Models\PurchaseItem');
    }
}
