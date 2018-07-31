<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayerStat extends Model
{
    // Table Name
    protected $table = 'player_stats';

    // Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = true;

    public function player() {
        return $this->belongsTo('App\Player');
    }
}
