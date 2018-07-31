<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayerMatch extends Model
{
    protected $table = 'player_matches';

    // Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = true;

    
    public function player() {
        return $this->belongsTo('App\Player');
    }
}
