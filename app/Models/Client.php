<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'id', 'partner_id', 'business_name', 'dui', 'registry_no', 'person_type', 'seller_id'
    ];

    public function partner(){
        return $this->belongsTo('App\Models\Partner');
    }

    public function seller(){
        return $this->belongsTo('App\Models\Seller');
    }

    public function bill(){
        return $this->hasMany('App\Models\Bill');
    }

    public function specialPrice(){
        return $this->hasMany('App\Models\SpecialPrice');
    }
}
