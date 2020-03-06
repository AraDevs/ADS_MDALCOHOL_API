<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'id', 'name', 'state'
    ];

    public function municipality(){
        return $this->hasMany('App\Models\Municipality');
    }
}
