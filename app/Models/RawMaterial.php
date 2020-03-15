<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $table = 'raw_materials';
    protected $fillable = [
        'id', 'inventory_id', 'provider_id'
    ];

    public function inventory(){
        return $this->belongsTo('App\Models\Inventory');
    }

    public function provider(){
        return $this->belongsTo('App\Models\Provider');
    }
}
