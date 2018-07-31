<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $table = 'players';

    // Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = true;

    public $incrementing = false;

    public function stats() {
        return $this->hasMany('App\PlayerStat');
    }

    // Every build has a hero
    public function matches(){
        return $this->hasMany('App\PlayerMatch');
    }

    public function teams(){
        return $this->hasMany('App\PlayerTeam');
    }
}
