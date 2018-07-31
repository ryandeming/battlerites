<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlayerTeam extends Model
{
    // Table Name
    protected $table = 'player_teams';

    // Primary Key
    public $primaryKey = 'id';

    // Timestamps
    public $timestamps = true;
}
