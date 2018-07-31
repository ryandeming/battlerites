<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Player;
use App\PlayerStat;
use App\PlayerTeam;
use App\PlayerMatch;

class PlayersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function retrieve($playerName = null, $playerId = null)
    {
       if($playerName != null) {
            if($player = Player::where('username', $playerName)->first() != null) {
                $player = Player::where('username', $playerName)->first();
            } else {
                $this->store($playerName);
                $player = Player::where('username', $playerName)->first();
            }
        } else {
            return;
        }
        if($player != null) {
        $now = new \DateTime();
        if((strtotime($now->format('Y-m-d H:i:s')) - strtotime($player->updated_at)) > 900) {
            $this->update($playerName);
            $player = Player::where('username', $playerName)->first();
        }
        $pageTitle = $player->username .'\'s stats and match history';
        if(count($player->matches) > 0) {
            $return['matches'] = PlayerMatch::where('player_id', $player->id)->orderBy('date', 'DESC')->paginate(5);
        } else {
            $return['matches'] = null;
        }
        $return['teams'] = $player->teams;
        $return['stats'] = $player->stats;
    
        return view('pages.player')->with(['player' => $player, 'matches' => $return['matches'], 'teams' => $return['teams'], 'stats' => $return['stats'], 'title' => $pageTitle]);
    } else {
        return view('pages.player')->with(['player' => null, 'matches' => null, 'teams' => null, 'title' => 'player lookup']);
    }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($playerName = null, $playerId = null)
    {
            if($playerName != null) {
                $player = Player::where('username', $playerName)->first();
            } elseif($playerId != null) {
                $player = Player::where('id', $playerId)->first();
            }
            if($player == null) {
                $player = new Player;
            }
            $lookup = $this->getPlayerStats($playerName, $playerId);
            if($lookup == null) {
                return;
            }
            $player->id = $lookup[0]['id'];
            $player->wins = $lookup[0]['totalWins'];
            $player->losses = $lookup[0]['totalLosses'];
            $player->username = $lookup[0]['username'];
            
            if($lookup != null) {
                foreach($lookup as $hero) {
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

            if($playerName == null) {
                $player->updated_at = "2018-03-14 05:06:51";
                $player->save();
                return;
            }
            $matches = $this->getMatchesById($player->id);
            $teams = $this->getPlayerRankById($player->id);
            if(($matches == null) || ($teams == null)) {
                $player->save();
            }

            if($teams != null) {
                foreach($teams as $team) {
                    if(PlayerTeam::where(['player_id' => $player->id, 'team_id' => $team['team_id']])->first()) {
                        $playerTeam = PlayerTeam::where(['player_id' => $player->id, 'team_id' => $team['team_id']])->first();
                    } else {
                        $playerTeam = new PlayerTeam;
                        $playerTeam->player_id = $player->id;
                    }
                    if($team['team_id'] != null) {
                        $playerTeam->team_id = $team['team_id'];
                        $playerTeam->name = $team['name'];
                        $playerTeam->league = $team['league'];
                        $playerTeam->league_id = $team['leagueId'];
                        $playerTeam->division = $team['division'];
                        $playerTeam->rating = $team['rating'];
                        $playerTeam->type = $team['type'];
                        $playerTeam->season = $team['season'];
                        $playerTeam->save();
                    }
                }
            }
            $team = $this->getLastSeasonRank($player->id, 6);
            if($team != null) {
                if(PlayerTeam::where(['player_id' => $player->id, 'team_id' => $team['team_id']])->first()) {
                    $playerTeam = PlayerTeam::where(['player_id' => $player->id, 'team_id' => $team['team_id']])->first();
                } else {
                    $playerTeam = new PlayerTeam;
                    $playerTeam->player_id = $player->id;
                }
                if($team['team_id'] != null) {
                    $playerTeam->league = $team['league'];
                    $playerTeam->league_id = $team['leagueId'];
                    $playerTeam->division = $team['division'];
                    $playerTeam->rating = $team['rating'];
                    $playerTeam->type = $team['type'];
                    $playerTeam->season = $team['season'];
                    $playerTeam->save();
                }
            }
            if($matches != null) {
                foreach($matches as $matchy) {
                    if(PlayerMatch::where(['player_id' => $matchy['playerId'], 'match_id' => $matchy['id']])->first()) {

                    } else {
                        if($matchy['hero'] == 'Unknown') {

                        } else {
                            $match = new PlayerMatch;
                            $match->player_id = $matchy['playerId'];
                            $match->match_id = $matchy['id'];
                            $match->hero_name = $matchy['hero'];
                            $match->result = $matchy['win'];
                            $match->damage_done = $matchy['damageDone'];
                            $match->damage_received = $matchy['damageReceived'];
                            $match->healing_done = $matchy['healingDone'];
                            $match->healing_received = $matchy['healingReceived'];
                            $match->disables_done = $matchy['disablesDone'];
                            $match->disables_received = $matchy['disablesReceived'];
                            $match->kills = $matchy['kills'];
                            $match->deaths = $matchy['deaths'];
                            $match->score = $matchy['score'];
                            $match->date = $matchy['time'];
                            $match->type = $matchy['type'];
                            $match->save();
                        }
                    }
                }
            }
            $player->save();
    }

    public function update($playerName = null) {
        if($playerName != null) {
            $player = Player::where('username', $playerName)->first();
        } else {
            return;
        }
        if($player == null) {
            $player = new Player;
        }
        $lookup = $this->getPlayerStats($playerName);
        if($lookup == null) {
            $player->save();
        }
        $player->id = $lookup[0]['id'];
        if((($player->wins == $lookup[0]['totalWins']) && ($player->losses == $lookup[0]['totalLosses'])) && ($player->updated_at != "2018-03-14 05:06:51")) {
            $now = new \DateTime();
            $now = $now->format('Y-m-d H:i:s');
            $player->updated_at = $now;
            $player->save();
            return;
        }
        $player->wins = $lookup[0]['totalWins'];
        $player->losses = $lookup[0]['totalLosses'];
        $player->username = $playerName;
        $matches = $this->getMatchesById($player->id);
        $teams = $this->getPlayerRankById($player->id);
        if(($matches == null) || ($teams == null)) {
            $player->save();
        }
        if($lookup != null) {
            foreach($lookup as $hero) {
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
        $updatePrevious = true;
        if($teams != null) {
            foreach($teams as $team) {
                if(PlayerTeam::where(['player_id' => $player->id, 'team_id' => $team['team_id']])->first()) {
                    $playerTeam = PlayerTeam::where(['player_id' => $player->id, 'team_id' => $team['team_id']])->first();
                } else {
                    $playerTeam = new PlayerTeam;
                    $playerTeam->player_id = $player->id;
                }
                if($team['team_id'] != null) {
                    $playerTeam->team_id = $team['team_id'];
                    $playerTeam->name = $team['name'];
                    $playerTeam->league = $team['league'];
                    $playerTeam->league_id = $team['leagueId'];
                    $playerTeam->division = $team['division'];
                    $playerTeam->rating = $team['rating'];
                    $playerTeam->type = $team['type'];
                    $playerTeam->season = $team['season'];
                    $playerTeam->save();
                }
                if($team['season'] == 6) {
                    $updatePrevious = false;
                }
            }
        }
        if($updatePrevious == true) {
            $team = $this->getLastSeasonRank($player->id, 6);
        
            if($team != null) {
                if(PlayerTeam::where(['player_id' => $player->id, 'team_id' => $team['team_id']])->first()) {
                    $playerTeam = PlayerTeam::where(['player_id' => $player->id, 'team_id' => $team['team_id']])->first();
                } else {
                    $playerTeam = new PlayerTeam;
                    $playerTeam->player_id = $player->id;
                }
                if($team['team_id'] != null) {
                    $playerTeam->league = $team['league'];
                    $playerTeam->league_id = $team['leagueId'];
                    $playerTeam->division = $team['division'];
                    $playerTeam->rating = $team['rating'];
                    $playerTeam->type = $team['type'];
                    $playerTeam->season = $team['season'];
                    $playerTeam->save();
                }
            }
        }
        if($matches != null) {
            foreach($matches as $matchy) {
                if(PlayerMatch::where(['player_id' => $matchy['playerId'], 'match_id' => $matchy['id']])->first()) {

                } else {
                    
                    if($matchy['hero'] == 'Unknown') {

                    } else {
                        $match = new PlayerMatch;
                        $match->player_id = $matchy['playerId'];
                        $match->match_id = $matchy['id'];
                        $match->hero_name = $matchy['hero'];
                        $match->result = $matchy['win'];
                        $match->damage_done = $matchy['damageDone'];
                        $match->damage_received = $matchy['damageReceived'];
                        $match->healing_done = $matchy['healingDone'];
                        $match->healing_received = $matchy['healingReceived'];
                        $match->disables_done = $matchy['disablesDone'];
                        $match->disables_received = $matchy['disablesReceived'];
                        $match->kills = $matchy['kills'];
                        $match->deaths = $matchy['deaths'];
                        $match->score = $matchy['score'];
                        $match->date = $matchy['time'];
                        $match->type = $matchy['type'];
                        $match->save();
                    }
                }
            }
        }
        $now = new \DateTime();
        $now = $now->format('Y-m-d H:i:s');
        $player->updated_at = $now;
        $player->save();
    }

    public function populate() {
        $matches = PlayerMatch::all();
        foreach($matches as $match) {
            $player = Player::where('id', $match->player_id)->first();
            if($player == null) {
                $this->store(null, $match->player_id);
                return;
            }
        }
        return;
    }

    public function getPlayerStats($playerName = null, $playerId = null) {
        if($playerName != null) {
            $json_url = "https://api.developer.battlerite.com/shards/global/players?filter[playerNames]=".urlencode(urlencode($playerName));
        } elseif($playerId != null) {
            $json_url = "https://api.developer.battlerite.com/shards/global/players?filter[playerIds]=".$playerId;
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
        $result = json_decode($result, true);
        // Notes
        // 2 = total wins, 3 = total losses, 16 = rank3v3wins, 17 = rank3v3losses, 14 = rank2v2wins, 15 = rank2v2losses
        
        if($this->multiKeyExists('id', $result)) {
        // Get player ID for team lookup
        $player[0]['totalWins'] = $player[0]['totalLosses'] = $player[0]['3v3Wins'] = $player[0]['3v3Losses'] = $player[0]['2v2Wins'] = $player[0]['2v2Losses'] = 0;
        $player[0]['id'] = $result['data'][0]['id'];
        $player[0]['username'] = $result['data'][0]['attributes']['name'];
        if($this->multiKeyExists('2', $result)) {
            $player[0]['totalWins'] = $result['data'][0]['attributes']['stats']['2'];
        }
        if($this->multiKeyExists('3', $result)) {
            $player[0]['totalLosses'] = $result['data'][0]['attributes']['stats']['3'];
        }
        if($this->multiKeyExists('16', $result)) {
            $player[0]['3v3Wins'] = $result['data'][0]['attributes']['stats']['16'];
        }
        if($this->multiKeyExists('17', $result)) {
            $player[0]['3v3Losses'] = $result['data'][0]['attributes']['stats']['17'];
        }
        if($this->multiKeyExists('14', $result)) {
            $player[0]['2v2Wins'] = $result['data'][0]['attributes']['stats']['14'];
        }
        if($this->multiKeyExists('15', $result)) {
            $player[0]['2v2Losses'] = $result['data'][0]['attributes']['stats']['15'];
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
        $heroArray[26]['name'] = 'ulric';
        $heroArray[26]['id'] = '039';
            
        $i=1;
        foreach($heroArray as $heroData) {
            $wins = '12'.$heroData['id'];
            $losses = '13'.$heroData['id'];
            $heroName = $heroData['name'];
            $player[$i]['hero'] = $heroName;
            if($this->multiKeyExists($wins, $result)) {
            $player[$i]['wins'] = $result['data'][0]['attributes']['stats'][$wins];
            } else { $player[$i]['wins'] = 0; }
            if($this->multiKeyExists($losses, $result)) {
            $player[$i]['losses'] = $result['data'][0]['attributes']['stats'][$losses];
            } else { $player[$i]['losses'] = 0; }
            $i++;
        }
        // Get and Set Character wins and losses

        return $player;
        } else {
            return null;
        }
    }

    function getPlayerRankById($playerId, $season = 7) {
        $json_url = "https://api.developer.battlerite.com/shards/global/teams?tag[playerIds]=".$playerId."&tag[season]=".$season;
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
        //echo '<pre>'.print_r($results, true).'</pre>';
        $rank = array();
        $rank['2v2LeagueId'] = $rank['2v2Rating'] = $rank['2v2Division'] = $rank['3v3LeagueId'] = $rank['3v3Rating'] = $rank['3v3Division'] = 0;    
            //check for ranking information
        if($this->multiKeyExists('league', $results)) {
            $i = 0;
            //check if user has teams
            if(count($results['data']) > 0) {
                //loop through teams
                foreach($results['data'] as $result) {
                    //get solo ranking
                    if(count($result['attributes']['stats']['members']) == 1) {
                        if($result['attributes']['stats']['placementGamesLeft'] > 0) {
                            $rank[$i]['team_id'] = $result['id'];
                            $rank[$i]['name'] = $rank[$i]['league'] = $rank[$i]['leagueId'] = $rank[$i]['division'] = $rank[$i]['rating'] = '';
                            $rank[$i]['type'] = "solo";
                            $rank[$i]['season'] = $season;
                            $i++;
                        } else {
                            $rank[$i]['team_id'] = $result['id'];
                            $rank[$i]['name'] = '';
                            $rank[$i]['league'] = $this->getRankLeague($result['attributes']['stats']['league']);    
                            $rank[$i]['leagueId'] = $result['attributes']['stats']['league'];
                            $rank[$i]['division'] = $result['attributes']['stats']['division'];
                            $rank[$i]['rating'] = $result['attributes']['stats']['divisionRating'];
                            $rank[$i]['type'] = "solo";
                            $rank[$i]['season'] = $season;
                            $i++;
                        }
                    }
    
                    //get 2v2 ranking
                    if(count($result['attributes']['stats']['members']) == 2) {
                        if($rank['2v2LeagueId'] <= $result['attributes']['stats']['league']) {
                            if($rank['2v2Division'] <= $result['attributes']['stats']['division']) {
                                if($rank['2v2Rating'] <= $result['attributes']['stats']['divisionRating']) {
                                    if($result['attributes']['stats']['placementGamesLeft'] > 0) {
                                        $rank[$i]['team_id'] = $result['id'];
                                        $rank[$i]['name'] = "";
                                        $rank[$i]['league'] = "Placements";
                                        $rank[$i]['leagueId'] = $rank[$i]['rating'] = '0';
                                        $rank[$i]['division'] = '10';
                                        $rank[$i]['type'] = "2v2";
                                        $rank[$i]['season'] = $season;
                                        $i++;
                                    } else {
                                    $rank[$i]['team_id'] = $result['id'];
                                    $rank[$i]['name'] = $result['attributes']['name'];
                                    $rank[$i]['league'] = $this->getRankLeague($result['attributes']['stats']['league']);    
                                    $rank[$i]['leagueId'] = $result['attributes']['stats']['league'];
                                    $rank[$i]['division'] = $result['attributes']['stats']['division'];
                                    $rank[$i]['rating'] = $result['attributes']['stats']['divisionRating'];
                                    $rank[$i]['type'] = "2v2";
                                    $rank[$i]['season'] = $season;
                                    $i++;
                                    }
                                }
                            }
                        }
                    }

                    // get 3v3 ranking
                    if(count($result['attributes']['stats']['members']) == 3) {
                        if($rank['3v3LeagueId'] <= $result['attributes']['stats']['league']) {
                            if($rank['3v3Division'] <= $result['attributes']['stats']['division']) {
                                if($rank['3v3Rating'] <= $result['attributes']['stats']['divisionRating']) {
                                    if($result['attributes']['stats']['placementGamesLeft'] > 0) {
                                        $rank[$i]['team_id'] = $result['id'];
                                        $rank[$i]['name'] = "";
                                        $rank[$i]['league'] = "Placements";
                                        $rank[$i]['leagueId'] = $rank[$i]['rating'] = '0';
                                        $rank[$i]['division'] = '10';
                                        $rank[$i]['type'] = "3v3";
                                        $rank[$i]['season'] = $season;
                                        $i++;
                                    } else {
                                        $rank[$i]['team_id'] = $result['id'];
                                        $rank[$i]['name'] = $result['attributes']['name'];
                                        $rank[$i]['league'] = $this->getRankLeague($result['attributes']['stats']['league']);    
                                        $rank[$i]['leagueId'] = $result['attributes']['stats']['league'];
                                        $rank[$i]['division'] = $result['attributes']['stats']['division'];
                                        $rank[$i]['rating'] = $result['attributes']['stats']['divisionRating'];
                                        $rank[$i]['type'] = "3v3";
                                        $rank[$i]['season'] = $season;
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }
                return $rank;
            // if user only has 1 team, get that rank and display it
            } else {
                return null;
            }
        }
    }

    function getMatchesById($playerId) {
        $json_url = "https://api.developer.battlerite.com/shards/global/matches?&sort=-createdAt&sort=desc&page[limit]=50&filter[playerIds]=".$playerId;
        //$json_url = "https://api.developer.battlerite.com/shards/global/matches/3890417D995C43948FFB4AD8431E1E5E";
        //$json_url = " https://cdn.gamelockerapp.com/stunlock-studios-battlerite/global/2018/02/15/20/51/f4aefb02-1291-11e8-9233-0a5864610581-telemetry.json";
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
        //return print_r($result);
        $results = json_decode($result, true);
        //$result = json_encode($result, JSON_PRETTY_PRINT);
        //$datas = $result['included'];
        $matches = array();
        //return $results;
        $i = 0;
        //return $results;
        // Actor IDs
        $heroArray[0]['name'] = 'lucie';
        $heroArray[0]['id'] = '467463015';
        $heroArray[1]['name'] = 'sirius';
        $heroArray[1]['id'] = '259914044';
        $heroArray[2]['name'] = 'iva';
        $heroArray[2]['id'] = '842211418';
        $heroArray[3]['name'] = 'jade';
        $heroArray[3]['id'] = '65687534';
        $heroArray[4]['name'] = 'ruh kaan';
        $heroArray[4]['id'] = '550061327';
        $heroArray[5]['name'] = 'oldur';
        $heroArray[5]['id'] = '1908945514';
        $heroArray[6]['name'] = 'ashka';
        $heroArray[6]['id'] = '1';
        $heroArray[7]['name'] = 'varesh';
        $heroArray[7]['id'] = '369797039';
        $heroArray[8]['name'] = 'pearl';
        $heroArray[8]['id'] = '44962063';
        $heroArray[9]['name'] = 'taya';
        $heroArray[9]['id'] = '154382530';
        $heroArray[10]['name'] = 'poloma';
        $heroArray[10]['id'] = '1134478706';
        $heroArray[11]['name'] = 'croak';
        $heroArray[11]['id'] = '1208445212';
        $heroArray[12]['name'] = 'freya';
        $heroArray[12]['id'] = '1606711539';
        $heroArray[13]['name'] = 'jumong';
        $heroArray[13]['id'] = '39373466';
        $heroArray[14]['name'] = 'shifu';
        $heroArray[14]['id'] = '763360732';
        $heroArray[15]['name'] = 'ezmo';
        $heroArray[15]['id'] = '1377055301';
        $heroArray[16]['name'] = 'bakko';
        $heroArray[16]['id'] = 1422481252;
        $heroArray[17]['name'] = 'rook';
        $heroArray[17]['id'] = '1318732017';
        $heroArray[18]['name'] = 'pestilus';
        $heroArray[18]['id'] = '1649551456';
        $heroArray[19]['name'] = 'destiny';
        $heroArray[19]['id'] = '870711570';
        $heroArray[20]['name'] = 'raigon';
        $heroArray[20]['id'] = '1749055646';
        $heroArray[21]['name'] = 'blossom';
        $heroArray[21]['id'] = '543520739';
        $heroArray[22]['name'] = 'thorn';
        $heroArray[22]['id'] = '1463164578';
        $heroArray[23]['name'] = 'zander';
        $heroArray[23]['id'] = '1496688063';
        $heroArray[24]['name'] = 'alysia';
        $heroArray[24]['id'] = '613085868';
        $heroArray[25]['name'] = 'jamila';
        $heroArray[25]['id'] = '1661996559';

        if($this->multiKeyExists('data', $results)) {
            foreach($results['data'] as $result) {
                if($result['type'] == 'match') {
                    $matches[$i]['time'] = $result['attributes']['createdAt'];
                    $matches[$i]['team1id'] = $result['relationships']['rosters']['data'][0]['id'];
                    $matches[$i]['team2id'] = $result['relationships']['rosters']['data'][1]['id'];
                    $matches[$i]['type'] = $result['attributes']['stats']['type'];
                    $matches[$i]['id'] = $result['id'];
                    $matchIds[$i]['id'] = $result['id'];
                    $matchIds[$i]['team1id'] = $result['relationships']['rosters']['data'][0]['id'];
                    $matchIds[$i]['team2id'] = $result['relationships']['rosters']['data'][1]['id'];
                    $matchIds[$i]['type'] = $result['attributes']['stats']['type'];
                    $matchIds[$i]['time'] = $result['attributes']['createdAt'];
                    $j=0;
                    foreach($result['relationships']['rounds']['data'] as $round) {
                        $matches[$i]['rounds'][$j]['id'] = $round['id'];
                        $j++;
                    }
                }
                $i++;
            }
            $rosterCounter = 0;
            $rosterIds = null;
            foreach($results['included'] as $result) {          
                if($result['type'] == 'round') {
                    $i=0;
                    foreach($matches as $match) {
                        $j=0;
                        foreach($match['rounds'] as $round) {
                            if($round['id'] == $result['id']) {
                                $matches[$i]['rounds'][$j]['winner'] = $result['attributes']['stats']['winningTeam'];
                            }
                            $j++;
                        }
                        $i++;
                    }
                }
            }
            foreach($results['included'] as $result) {
                if($result['type'] == 'participant') {
                    $rosterIds[$rosterCounter]['hero'] = 'Unknown';
                    $rosterIds[$rosterCounter]['id'] = $result['id'];
                    foreach($heroArray as $hero) {
                        if($hero['id'] == $result['attributes']['actor']) {
                            $rosterIds[$rosterCounter]['hero'] = $hero['name'];
                        } 
                    }
                    $rosterIds[$rosterCounter]['playerId'] = $result['relationships']['player']['data']['id'];
                    if($this->multiKeyExists('score', $result)) {
                        $rosterIds[$rosterCounter]['score'] = $result['attributes']['stats']['score'];
                        $rosterIds[$rosterCounter]['healingDone'] = $result['attributes']['stats']['healingDone'];
                        $rosterIds[$rosterCounter]['healingReceived'] = $result['attributes']['stats']['healingReceived'];
                        $rosterIds[$rosterCounter]['damageDone'] = $result['attributes']['stats']['damageDone'];
                        $rosterIds[$rosterCounter]['damageReceived'] = $result['attributes']['stats']['damageReceived'];
                        $rosterIds[$rosterCounter]['disablesDone'] = $result['attributes']['stats']['disablesDone'];
                        $rosterIds[$rosterCounter]['disablesReceived'] = $result['attributes']['stats']['disablesReceived'];
                        $rosterIds[$rosterCounter]['kills'] = $result['attributes']['stats']['kills'];
                        $rosterIds[$rosterCounter]['deaths'] = $result['attributes']['stats']['deaths'];
                    } else {
                        $rosterIds[$rosterCounter]['score'] = 0;
                        $rosterIds[$rosterCounter]['healingDone'] = 0;
                        $rosterIds[$rosterCounter]['healingReceived'] = 0;
                        $rosterIds[$rosterCounter]['damageDone'] = 0;
                        $rosterIds[$rosterCounter]['damageReceived'] = 0;
                        $rosterIds[$rosterCounter]['disablesDone'] = 0;
                        $rosterIds[$rosterCounter]['disablesReceived'] = 0;
                        $rosterIds[$rosterCounter]['kills'] = 0;
                        $rosterIds[$rosterCounter]['deaths'] = 0;
                    }
                    $rosterCounter++;

                }
            }
            foreach($results['included'] as $result) {
                if($result['type'] == 'roster') {
                    $x=0;
                    foreach($matches as $match) {
                        if(($match['team1id'] == $result['id']) || ($match['team2id'] == $result['id'])) {
                            if($rosterIds != null) {
                                foreach($rosterIds as $player) {
                                    foreach($result['relationships']['participants']['data'] as $stupid) {
                                        if($player['id'] == $stupid['id']) {
                                            foreach($matchIds as $matchId) {
                                                if(($matchId['team1id'] == $result['id']) || ($matchId['team2id'] == $result['id'])) {
                                                    $matchesy[$x]['id'] = $matchId['id'];
                                                    $matchesy[$x]['time'] = $matchId['time'];
                                                    $matchesy[$x]['type'] = $matchId['type'];
                                                }
                                            }
                                            $matchesy[$x]['playerId'] = $player['playerId'];
                                            $matchesy[$x]['win'] = $result['attributes']['won'];
                                            $matchesy[$x]['hero'] = $player['hero'];
                                            $matchesy[$x]['score'] = $player['score'];
                                            $matchesy[$x]['healingDone'] = $player['healingDone'];
                                            $matchesy[$x]['healingReceived'] = $player['healingReceived'];
                                            $matchesy[$x]['damageDone'] = $player['damageDone'];
                                            $matchesy[$x]['damageReceived'] = $player['damageReceived'];
                                            $matchesy[$x]['disablesDone'] = $player['disablesDone'];
                                            $matchesy[$x]['disablesReceived'] = $player['disablesReceived'];
                                            $matchesy[$x]['kills'] = $player['kills'];
                                            $matchesy[$x]['deaths'] = $player['deaths'];
                                        }
                                    }
                                    $x++;
                                }
                            } else {
                                return null;
                            }
                        }
                    }
                }
            }
            return $matchesy;
        }
        return null;
    }

    function getLastSeasonRank($playerId, $season) {
        $json_url = "https://api.developer.battlerite.com/shards/global/teams?tag[playerIds]=".$playerId."&tag[season]=".$season;
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
        //echo '<pre>'.print_r($results, true).'</pre>';
        $rank = array();
        $rank['2v2LeagueId'] = $rank['2v2Rating'] = $rank['3v3LeagueId'] = $rank['3v3Rating'] = 0;   
        $rank['2v2Division'] = $rank['3v3Division'] = 6;
            //check for ranking information
        if($this->multiKeyExists('league', $results)) {
            
            //check if user has teams
            if(count($results['data']) > 1) {
                //loop through teams
                $highestRank['leagueId'] = 0;
                $highestRank['division'] = 5;
                $highestRank['rating'] = 0;
                foreach($results['data'] as $result) {
                    //get solo ranking
                    if(count($result['attributes']['stats']['members']) == 1) {
                        $rank['team_id'] = $result['id'];
                        $rank['league'] = $this->getRankLeague($result['attributes']['stats']['topLeague']);    
                        $rank['leagueId'] = $result['attributes']['stats']['topLeague'];
                        $rank['division'] = $result['attributes']['stats']['topDivision'];
                        $rank['rating'] = $result['attributes']['stats']['topDivisionRating'];
                    }
    
                    //get 2v2 ranking
                    if(count($result['attributes']['stats']['members']) == 2) {
                        if($rank['2v2LeagueId'] <= $result['attributes']['stats']['topLeague']) {
                            if($rank['2v2Division'] >= $result['attributes']['stats']['topDivision']) {
                                if($rank['2v2Rating'] <= $result['attributes']['stats']['topDivisionRating'])
                                    $rank['2v2Team_id'] = $result['id'];
                                    $rank['2v2Name'] = $result['attributes']['name'];
                                    $rank['2v2League'] = $this->getRankLeague($result['attributes']['stats']['topLeague']);    
                                    $rank['2v2LeagueId'] = $result['attributes']['stats']['topLeague'];
                                    $rank['2v2Division'] = $result['attributes']['stats']['topDivision'];
                                    $rank['2v2Rating'] = $result['attributes']['stats']['topDivisionRating'];
                            }
                        }
                    }
                    // get 3v3 ranking
                    if(count($result['attributes']['stats']['members']) == 3) {
                        if($rank['3v3LeagueId'] <= $result['attributes']['stats']['topLeague']) {
                            if($rank['3v3Division'] >= $result['attributes']['stats']['topDivision']) {
                                if($rank['3v3Rating'] <= $result['attributes']['stats']['topDivisionRating'])
                                    $rank['3v3Team_id'] = $result['id'];
                                    $rank['3v3Name'] = $result['attributes']['name'];
                                    $rank['3v3League'] = $this->getRankLeague($result['attributes']['stats']['topLeague']);    
                                    $rank['3v3LeagueId'] = $result['attributes']['stats']['topLeague'];
                                    $rank['3v3Division'] = $result['attributes']['stats']['topDivision'];
                                    $rank['3v3Rating'] = $result['attributes']['stats']['topDivisionRating'];
                            }
                        }
                    }
                }

                //check if 3v3 is highest
                if($highestRank['leagueId'] <= $rank['3v3LeagueId']) {
                    if($highestRank['leagueId'] == $rank['3v3LeagueId']) {
                        if($highestRank['division'] >= $rank['3v3Division']) {
                            $highestRank['team_id'] = $rank['3v3Team_id'];
                            $highestRank['league'] = $rank['3v3League'];    
                            $highestRank['leagueId'] = $rank['3v3LeagueId'];
                            $highestRank['division'] = $rank['3v3Division'];
                            $highestRank['rating'] = $rank['3v3Rating'];
                            $highestRank['type'] = "3V3 TEAM";
                            $highestRank['season'] = $season;
                        }
                    } else {
                        $highestRank['team_id'] = $rank['3v3Team_id'];
                        $highestRank['league'] = $rank['3v3League'];    
                        $highestRank['leagueId'] = $rank['3v3LeagueId'];
                        $highestRank['division'] = $rank['3v3Division'];
                        $highestRank['rating'] = $rank['3v3Rating'];
                        $highestRank['type'] = "3V3 TEAM";
                        $highestRank['season'] = $season;
                    }
                }
                //check if 2v2 is higher
                if($highestRank['leagueId'] <= $rank['2v2LeagueId']) {
                    if($highestRank['leagueId'] == $rank['2v2LeagueId']) {
                        if($highestRank['division'] >= $rank['2v2Division']) {
                            $highestRank['team_id'] = $rank['2v2Team_id'];
                            $highestRank['league'] = $rank['2v2League'];    
                            $highestRank['leagueId'] = $rank['2v2LeagueId'];
                            $highestRank['division'] = $rank['2v2Division'];
                            $highestRank['rating'] = $rank['2v2Rating'];
                            $highestRank['type'] = "2V2 TEAM";
                            $highestRank['season'] = $season;
                        }
                    } else {
                        $highestRank['team_id'] = $rank['2v2Team_id'];
                        $highestRank['league'] = $rank['2v2League'];    
                        $highestRank['leagueId'] = $rank['2v2LeagueId'];
                        $highestRank['division'] = $rank['2v2Division'];
                        $highestRank['rating'] = $rank['2v2Rating'];
                        $highestRank['type'] = "2V2 TEAM";
                        $highestRank['season'] = $season;
                    }
                }
                //check if solo is higher
                if($highestRank['leagueId'] <= $rank['leagueId']) {
                    if($highestRank['leagueId'] == $rank['leagueId']) {
                        if($highestRank['division'] >= $rank['division']) {
                            $highestRank['team_id'] = $rank['team_id'];
                            $highestRank['league'] = $rank['league'];    
                            $highestRank['leagueId'] = $rank['leagueId'];
                            $highestRank['division'] = $rank['division'];
                            $highestRank['rating'] = $rank['rating'];
                            $highestRank['type'] = "SOLO";
                            $highestRank['season'] = $season;
                        }
                    } else {
                        $highestRank['team_id'] = $rank['team_id'];
                        $highestRank['league'] = $rank['league'];    
                        $highestRank['leagueId'] = $rank['leagueId'];
                        $highestRank['division'] = $rank['division'];
                        $highestRank['rating'] = $rank['rating'];
                        $highestRank['type'] = "SOLO";
                        $highestRank['season'] = $season;
                    }
                }
                
                return $highestRank;
            // if user only has 1 team, get that rank and display it
            } else {
                $rank['highest'] = '';
                $rank['team_id'] = $results['data']['0']['id'];
                $rank['league'] = $this->getRankLeague($results['data'][0]['attributes']['stats']['league']);
                $rank['leagueId'] = $results['data'][0]['attributes']['stats']['league'];
                $rank['division'] = $results['data'][0]['attributes']['stats']['division'];
                $rank['rating'] = $results['data'][0]['attributes']['stats']['divisionRating'];
                $rank['season'] = $season;
                if(count($results['data'][0]['attributes']['stats']['members'] == 1)) {
                    $rank['type'] = "SOLO";
                }
                if(count($results['data'][0]['attributes']['stats']['members'] == 2)) {
                    $rank['type'] = "2V2 TEAM";
                }
                if(count($results['data'][0]['attributes']['stats']['members'] == 3)) {
                    $rank['type'] = "3V3 TEAM";
                }
                return $rank;
            }
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
                if ($this->multiKeyExists($key, $element)) {
                    return true;
                }
            }
    
        }
    
        return false;
    }

    
function getRankLeague($leagueId) {
    switch($leagueId) {
        case 0:
            return "Copper";
            break;
        case 1:
            return "Silver";
            break;
        case 2:
            return "Gold";
            break;
        case 3:
            return "Platinum";
            break;
        case 4:
            return "Diamond";
            break;
        case 5:
            return "Champion";
            break;
        case 6:
            return "Grand Champion";
            break;
    }
}

}
