<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Build extends Model
{
    //
    protected $table = 'builds';

    // Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = true;

    // Attach a build to a specific user (it's creator)
    public function user() {
        return $this->belongsTo('App\User');
    }

    // Every build has a hero
    public function hero(){
        return $this->hasOne('App\Hero');
    }

    public function ratings() {
        return $this->hasMany('App\Rating');
    }
}
