<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PlayerMatch;
use App\Player;
use App\PlayerStat;

class PlayerMatchesController extends Controller
{
    public function retrieve($matchId) {
        $players = PlayerMatch::where('match_id', $matchId)->paginate(10);
        $playerIds = "";
        foreach($players as $player) {
            $playerExists = Player::where('id', $player->player_id)->first();
            if($playerExists != null) {
                //$pageTitle = $player->player_id;
            } else {
                $playerIds .= $player->player_id.",";
                //app('App\Http\Controllers\PlayersController')->store(null, $player->player_id);
            }
        }
        $playerIds = substr($playerIds, 0, -1);
        $missingPlayers = getPlayerStats($playerIds);
        if($missingPlayers != null) {
            foreach($missingPlayers as $missingPlayer) {
                $player = new Player;
                $player->id = $missingPlayer[0]['id'];
                $player->wins = $missingPlayer[0]['totalWins'];
                $player->losses = $missingPlayer[0]['totalLosses'];
                $player->username = $missingPlayer[0]['username'];
                $player->updated_at = "2018-03-14 05:06:51";
                if($missingPlayer != null) {
                    foreach($missingPlayer as $hero) {
                        if(array_key_exists('wins', $hero)) {
                            if(PlayerStat::where(['player_id' => $player->id, 'hero_name' => $hero['hero']])->first()) {
                                $playerStat = PlayerStat::where(['player_id' => $player->id, 'hero_name' => $hero['hero']])->first();
                            } else {
                                $playerStat = new PlayerStat;
                                $playerStat->player_id = $player->id;
                            }
                            $playerStat->hero_name = $hero['hero'];
                            $playerStat->wins = $hero['wins'];
                            $playerStat->losses = $hero['losses'];
                            $playerStat->save();
                        }
                    }
                }
                $player->save();
            }
        }
        $pageTitle = "Match Lookup";
        return view('pages.match')->with(['players' => $players, 'title' => $pageTitle]);
    }

}

function getPlayerStats($playerIds = null) {
    if($playerIds != null) {
        $json_url = "https://api.developer.battlerite.com/shards/global/players?filter[playerIds]=".$playerIds;
    } else {
        return '';
    }
    $ch      = curl_init( $json_url );
    $options = array(
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => array( "Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiIxZDQ4YzEyMC0wMzA3LTAxMzYtZDVmNS0wYTU4NjQ2MTA1MDgiLCJpc3MiOiJnYW1lbG9ja2VyIiwiaWF0IjoxNTIwMjk3NzgzLCJwdWIiOiJzdHVubG9jay1zdHVkaW9zIiwidGl0bGUiOiJiYXR0bGVyaXRlIiwiYXBwIjoidGJkLWM0ZmI4OTg2LTc2YjgtNGZhMy05NGY0LTA1MmMzMjViYzBhMyIsInNjb3BlIjoiY29tbXVuaXR5IiwibGltaXQiOjEwfQ.JsoSNx4I0LwXJZgnoH-xGjgbKBYW5tIEMGY9WtG_JRI", "Accept: application/vnd.api+json" ),
    );
    curl_setopt_array( $ch, $options );

    $result = curl_exec( $ch );
    if($result == null) {
        return null;
    }
    $results = json_decode($result, true);
    // Notes
    // 2 = total wins, 3 = total losses, 16 = rank3v3wins, 17 = rank3v3losses, 14 = rank2v2wins, 15 = rank2v2losses
    $j = 0;
    if(multiKeyExists('id', $results)) {
        foreach($results['data'] as $result) {
            
    // Get player ID for team missingPlayer
    $player[$j][0]['totalWins'] = $player[$j][0]['totalLosses'] = $player[$j][0]['3v3Wins'] = $player[$j][0]['3v3Losses'] = $player[$j][0]['2v2Wins'] = $player[$j][0]['2v2Losses'] = 0;
    $player[$j][0]['id'] = $result['id'];
    $player[$j][0]['username'] = $result['attributes']['name'];
    if(multiKeyExists('2', $result)) {
        $player[$j][0]['totalWins'] = $result['attributes']['stats']['2'];
    }
    if(multiKeyExists('3', $result)) {
        $player[$j][0]['totalLosses'] = $result['attributes']['stats']['3'];
    }
    if(multiKeyExists('16', $result)) {
        $player[$j][0]['3v3Wins'] = $result['attributes']['stats']['16'];
    }
    if(multiKeyExists('17', $result)) {
        $player[$j][0]['3v3Losses'] = $result['attributes']['stats']['17'];
    }
    if(multiKeyExists('14', $result)) {
        $player[$j][0]['2v2Wins'] = $result['attributes']['stats']['14'];
    }
    if(multiKeyExists('15', $result)) {
        $player[$j][0]['2v2Losses'] = $result['attributes']['stats']['15'];
    }
    
    // Hero Names and ID's for loop
    $heroArray[0]['name'] = 'lucie';
    $heroArray[0]['id'] = '001';
    $heroArray[1]['name'] = 'sirius';
    $heroArray[1]['id'] = '002';
    $heroArray[2]['name'] = 'iva';
    $heroArray[2]['id'] = '003';
    $heroArray[3]['name'] = 'jade';
    $heroArray[3]['id'] = '004';
    $heroArray[4]['name'] = 'ruh kaan';
    $heroArray[4]['id'] = '005';
    $heroArray[5]['name'] = 'oldur';
    $heroArray[5]['id'] = '006';
    $heroArray[6]['name'] = 'ashka';
    $heroArray[6]['id'] = '007';
    $heroArray[7]['name'] = 'varesh';
    $heroArray[7]['id'] = '008';
    $heroArray[8]['name'] = 'pearl';
    $heroArray[8]['id'] = '009';
    $heroArray[9]['name'] = 'taya';
    $heroArray[9]['id'] = '010';
    $heroArray[10]['name'] = 'poloma';
    $heroArray[10]['id'] = '011';
    $heroArray[11]['name'] = 'croak';
    $heroArray[11]['id'] = '012';
    $heroArray[12]['name'] = 'freya';
    $heroArray[12]['id'] = '013';
    $heroArray[13]['name'] = 'jumong';
    $heroArray[13]['id'] = '014';
    $heroArray[14]['name'] = 'shifu';
    $heroArray[14]['id'] = '015';
    $heroArray[15]['name'] = 'ezmo';
    $heroArray[15]['id'] = '016';
    $heroArray[16]['name'] = 'bakko';
    $heroArray[16]['id'] = '017';
    $heroArray[17]['name'] = 'rook';
    $heroArray[17]['id'] = '018';
    $heroArray[18]['name'] = 'pestilus';
    $heroArray[18]['id'] = '019';
    $heroArray[19]['name'] = 'destiny';
    $heroArray[19]['id'] = '020';
    $heroArray[20]['name'] = 'raigon';
    $heroArray[20]['id'] = '021';
    $heroArray[21]['name'] = 'blossom';
    $heroArray[21]['id'] = '022';
    $heroArray[22]['name'] = 'thorn';
    $heroArray[22]['id'] = '025';
    $heroArray[23]['name'] = 'zander';
    $heroArray[23]['id'] = '035';
    $heroArray[24]['name'] = 'alysia';
    $heroArray[24]['id'] = '041';
    $heroArray[25]['name'] = 'jamila';
    $heroArray[25]['id'] = '043';
    
    $i=1;
    foreach($heroArray as $heroData) {
        $wins = '12'.$heroData['id'];
        $losses = '13'.$heroData['id'];
        $heroName = $heroData['name'];
        $player[$j][$i]['hero'] = $heroName;
        if(multiKeyExists($wins, $result)) {
        $player[$j][$i]['wins'] = $result['attributes']['stats'][$wins];
        } else { $player[$j][$i]['wins'] = 0; }
        if(multiKeyExists($losses, $result)) {
        $player[$j][$i]['losses'] = $result['attributes']['stats'][$losses];
        } else { $player[$j][$i]['losses'] = 0; }
        $i++;
    }
    // Get and Set Character wins and losses
    $j++;
        }
        return $player;
    } else {
        return null;
    }
}
function multiKeyExists($key, array $arr) {
        // is in base array?
        if (array_key_exists($key, $arr)) {
            return true;
        }
    
        // check arrays contained in this array
        foreach ($arr as $element) {
            if (is_array($element)) {
                if (multiKeyExists($key, $element)) {
                    return true;
                }
            }
    
        }
    
        return false;
    }
?>