<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $table = 'municipalities';

    protected $fillable = [
        'id', 'name', 'department_id', 'state'
    ];

    public function department(){
        return $this->belongsTo('App\Models\Department');
    }

    public function partner(){
        return $this->hasMany('App\Models\Partner');
    }
}
