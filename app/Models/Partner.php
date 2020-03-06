<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'partners';

    protected $fillable = [
        'id', 'name', 'address', 'municipality_id', 'nit', 'phone', 'state'
    ];

    public function municipality(){
        return $this->belongsTo('App\Models\Municipality');
    }

    public function client(){
        return $this->hasOne('App\Models\Client');
    }
    
    public function provider(){
        return $this->hasOne('App\Models\Provider');
    }
}
