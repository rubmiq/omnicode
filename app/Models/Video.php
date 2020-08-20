<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    public function tags(){
       return $this->hasMany('App\Models\Tag');
    }


}
