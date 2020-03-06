<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $table = 'providers';

    protected $fillable = [
        'id', 'partner_id', 'seller_phone'
    ];

    public function partner(){
        return $this->belongsTo('App\Models\Partner');
    }

    public function rawMaterial(){
        return $this->hasMany('App\Models\RawMaterial');
    }
}
