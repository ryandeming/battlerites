<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    //
    protected $table = 'heroes';

    // Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = false;

    public function builds(){
        return $this->hasMany('App\Build');
    }
    
    public function abilities(){
        return $this->hasMany('App\Ability');
    }

    public function battlerites(){
        return $this->hasMany('App\Battlerite');
    }
}
