<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    //
    protected $table = 'abilities';

    // Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = false;

    // Attach an ability to a specific hero
    public function hero(){
        return $this->belongsTo('App\Hero');
    }
}
