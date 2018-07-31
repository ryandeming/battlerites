<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Battlerite extends Model
{
    //
    protected $table = 'battlerites';

    // Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = false;

    // Attach an battlerite to a specific hero
    public function hero(){
        return $this->belongsTo('App\Hero');
    }
}
