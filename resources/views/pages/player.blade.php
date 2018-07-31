<?php 

use App\Hero;
use App\Player;
use App\PlayerStat;
use App\PlayerTeam;
use App\PlayerMatch;

?>
@extends('layouts.app')
@if($title)
    @section('title', $title)
@endif
@section('content')       
        @if($player != null)
            <?php
                $heroStats = sortHeroArray($stats);
                $highestRank = null;
                $rank = $rank2v2 = $rank3v3 = null;
                if(count($teams) > 1) {
                    $rank2v2 = new PlayerTeam;
                    $rank2v2->division = 5;
                    $rank3v3 = new PlayerTeam;
                    $rank3v3->division = 5;
                }
                foreach($teams as $team) {
                    if(($team->type) == 'solo' && ($team->season == 7)) {
                        if($team->league_id == 0 && $team->division == 5 && $team->rating == 0) {
                        $rank = null;
                        } else {
                            $rank = $team;
                        }
                    }
                    if($team->season == 6) {
                        if($team->league_id == 0 && $team->division == 5 && $team->rating == 0) {
                            $highestRank = null;
                        } else {
                            $highestRank = $team;
                        }
                    }
                    if($team->type == '2v2') {
                        if($rank2v2->league_id <= $team->league_id) {
                            if($rank2v2->division >= $team->division) {
                                if($rank2v2->rating <= $team->rating) {
                                    $rank2v2 = $team;
                                }
                            }
                        }
                    }
                    if($team->type == '3v3') {
                        if($rank3v3->league_id <= $team->league_id) {
                            if($rank3v3->division >= $team->division) {
                                if($rank3v3->rating <= $team->rating) {
                                    $rank3v3 = $team;
                                }
                            }
                        }
                    }
                }
            ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="player-stats-box content-block">
                            <div class="left">
                                @if($rank != null)
                                    @if($rank->league_id)
                                    <img src="{{asset('images/leagues/'.$rank->league_id.'.png')}}" class="league-img" alt="{{$rank->league}} league">
                                    @endif
                                @endif
                                <div class="player-info">
                                    <h2>{{$player->username}}</h2>
                                    @if($rank != null)
                                        @if($rank->league_id)
                                        <h3>
                                            {{$rank->league}} {{$rank->division}}
                                            <small>{{$rank->rating}} points</small>
                                        </h3>
                                        @else 
                                            <h3>In Placements</h3>
                                        @endif
                                    @else 
                                        <h3>Ranking Information Unavailable</h3>
                                    @endif
                                </div>
                            </div>
                            <div class="right">
                                    <p>
                                        <span class="wins">{{$player->wins}}</span> - <span class="losses">{{$player->losses}}</span>
                                        <small>Wins - Losses</span>
                                    </p>
                            </div>
                        </div>
                    </div>
                </div>
                @if($highestRank != null || $rank2v2 != null || $rank3v3 != null)
                <div class="row">
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <div class="content-block player-team-box">
                            <p class="highest">LAST SEASON BEST</p>
                            
                            @if($highestRank == null)
                                <p class="no-info text-center">No Information</p>
                                <p class="type">UNKNOWN</p>
                            @else
                            <p class="type">{{$highestRank->type}}</p>
                            <img src="{{asset('images/leagues/'.$highestRank->league_id.'.png')}}" class="league-img" alt="{{$highestRank->league_id}} league">
                            <div class="player-info">
                                <h2>{{$highestRank->league}} {{$highestRank->division}}</h2>
                                <small>{{$highestRank->rating}} points</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <div class="content-block player-team-box">
                            <p class="highest">CURRENT SEASON BEST</p>
                            <p class="type">2V2</p>
                            @if($rank2v2 == null)
                                <p class="text-center no-info">No Information</p>
                            @elseif($rank2v2->league_id == 10)
                                <p class="text-center no-info">In Placements</p>
                            @elseif((($rank2v2->league_id == 0) && ($rank2v2->division == 5) && ($rank2v2->rating == 0)) || $rank2v2 == null) 
                                <p class="text-center no-info">No Information</p>
                            @else
                            <img src="{{asset('images/leagues/'.$rank2v2->league_id.'.png')}}" class="league-img" alt="{{$rank2v2->league_id}} league">
                            <div class="player-info">
                                <h2>{{$rank2v2->league}} {{$rank2v2->division}}</h2>
                                <small>{{$rank2v2->rating}} points</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <div class="content-block player-team-box">
                            <p class="highest">CURRENT SEASON BEST</p>
                            <p class="type">3v3</p>
                            @if($rank3v3 == null)
                                <p class="text-center no-info">No Information</p>
                            @elseif($rank3v3->league_id == 10)
                                <p class="text-center no-info">In Placements</p>
                            @elseif((($rank3v3->league_id == 0) && ($rank3v3->division == 5) && ($rank3v3->rating == 0)) || $rank3v3 == null)
                            <p class="text-center no-info">No Information</p>
                            @else
                            <img src="{{asset('images/leagues/'.$rank3v3->league_id.'.png')}}" class="league-img" alt="{{$rank3v3->league_id}} league">
                            <div class="player-info">
                                <h2>{{$rank3v3->league}} {{$rank3v3->division}}</h2>
                                <small>{{$rank3v3->rating}} points</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                <div class="row">
                @foreach($heroStats as $stats)
                    @if(array_key_exists('hero', $stats))
                        <?php //$heroName = str_replace(" ", "-", $stats['hero']) 
                        $heroName = $stats['hero']; ?>
                        <div class="col-xs-6 col-sm-4 col-md-3 text-center">
                            <div class="content-block hero-stats-box">
                                <img src="{{asset('images/'.$heroName.'/icon.png')}}" class="hero-img" alt="{{$heroName}}">
                            <h3 class="hero-name">{{$heroName}}</h3>
                            <p>
                                <span class="wins">{{$stats['wins']}}</span> - <span class="losses">{{$stats['losses']}}</span>
                                <small>wins - losses</small>
                            </p>
                            </div>
                        </div>
                    @endif
                @endforeach
                </div>
        @else
        <p>Player not found</p>
        <h2>Search</h2>
            <div class="form-group">
                {{Form::label('playerName', 'Player Name')}}
                {{Form::text('playerName', '', ['class' => 'form-control', 'placeholder' => 'Player Name'])}}
            </div>
            {{Form::button('Search', ['class' => 'btn btn-primary', 'onclick' => 'lookupRedirect()'])}}
        @endif
@endsection

@section('sidebar')
    @if($matches != null)
            <div class="sidebar-item">
                <h3 class="bold mb-10">Recent Matches</h3>
                <div class="match-history">
                    @foreach($matches as $match)
                    <div class="match">
                        <img src="{{asset('images/'.$match->hero_name.'/icon.png')}}" class="hero-img" alt="{{$match->hero_name}}">
                        <p class="match-type">{{$match->type}}</p>
                        @if($match->result == 'true')
                            <p class="result win">Win</p>
                        @else
                            <p class="result loss">Loss</p>
                        @endif
                        <p>as {{$match->hero_name}}</p>
                        <p class="time">{{date("m-d-Y", strtotime($match->date))}}</p>
                        <p class="expand">Click to expand</p>
                        <div class="match-stats-box" style="display: none; clear:both;">
                            <p>Damage Done: <span class="bold lightgreen">{{$match->damage_done}}</span> <span class="right">Damage Received: <span class="bold orangered">{{$match->damage_received}}</span></span></p>
                            <p>Healing Done: <span class="bold lightgreen">{{$match->healing_done}}</span> <span class="right">Healing Received: <span class="bold orangered">{{$match->healing_received}}</span></span></p>
                            <p>Disables Done: <span class="bold lightgreen">{{$match->disables_done}}</span> <span class="right">Disables Received: <span class="bold orangered">{{$match->disables_received}}</span></span></p>
                            <p>Kills: <span class="bold lightgreen">{{$match->kills}}</span> <span class="right">Deaths: <span class="bold orangered">{{$match->deaths}}</span></span></p>
                            <p class="score bold text-center">Total Score: <span class="lightgreen">{{$match->score}}</span></p>
                            <a href="/match/{{$match->match_id}}">View full match details</a>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div style="margin: 0 auto; width: fit-content;">{{$matches->links()}}</div>
            </div>
        @endif
@endsection

<?php 

function sortHeroArray($arr) {
    $i=0;

    foreach($arr as $ar) {
        $sort[$i]['wins'] = $ar->wins;
        $sort[$i]['losses'] = $ar->losses;
        $sort[$i]['hero'] = $ar->hero_name;
        $i++;
    }
    usort($sort, function($a, $b) {
        return $b['wins'] - $a['wins'];
    });
    return $sort;
}

?>