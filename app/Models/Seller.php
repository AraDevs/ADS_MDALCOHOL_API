<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $table = 'sellers';
    protected $fillable = [
        'id', 'name', 'seller_code', 'state'
    ];

    public function client(){
        return $this->hasMany('App\Models\Client');
    }
}
