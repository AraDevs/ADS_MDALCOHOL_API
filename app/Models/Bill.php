<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bills';

    protected $fillable = [
        'id', 'client_id', 'bill_date','payment_type','bill_type','perception','state'
    ];

    public function client(){
        return $this->belongsTo('App\Models\Client');
    }

    public function billItem(){
        return $this->hasMany('App\Models\BillItem');
    }
}
