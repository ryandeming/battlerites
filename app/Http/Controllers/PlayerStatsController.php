<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlayerStatsController extends Controller
{
    public function store($playerId, $statsArray)
    {
            if($playerId != null) {
                $player = Player::where('api_id', $playerId)->first();
            } else {
                return;
            }
            foreach($statsArray as $stat) {
                if(PlayerStat::where('hero_name', $stat['hero'])->first() == null) {
                    $playerStat = new PlayerStat;
                    $playerStat->user_api_id = $playerId;
                } else {
                    $playerStat = PlayerStat::where('hero_name', $stat['hero'])->first();
                }
                $playerStat->wins = $stat['wins'];
                $playerStat->losses = $stat['losses'];
                $playerStat->save();
            }
    }
}
