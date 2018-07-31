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
<div class="row match-row">
    <div class="col-md-6">
        <div class="content-block">
            <h2>Winning Team</h2>
            <div class="match-history">
                @foreach($players as $player)
                    @if($player->result == 'true')
                        <?php 
                        $playerName = Player::where('id', $player->player_id)->first(); 
                        //$playerName = new Player;
                        //$playerName->username = "abc";?>
                    <div class="match">
                        <img src="{{asset('images/'.$player->hero_name.'/icon.png')}}" class="hero-img" alt="{{$player->hero_name}}">
                        <p class="match-type">{{$player->type}}</p>
                        <p class="result win">{{$playerName->username}}</p>
                        <p>as {{$player->hero_name}}</p>
                        <p class="time">{{date("m-d-Y", strtotime($player->date))}}</p>
                        <a href="/player/{{$playerName->username}}" class="expand">View player page</a>
                        <div class="match-stats-box" style="clear:both;">
                            <p>Damage Done: <span class="bold lightgreen">{{$player->damage_done}}</span> <span class="right">Damage Received: <span class="bold orangered">{{$player->damage_received}}</span></span></p>
                            <p>Healing Done: <span class="bold lightgreen">{{$player->healing_done}}</span> <span class="right">Healing Received: <span class="bold orangered">{{$player->healing_received}}</span></span></p>
                            <p>Disables Done: <span class="bold lightgreen">{{$player->disables_done}}</span> <span class="right">Disables Received: <span class="bold orangered">{{$player->disables_received}}</span></span></p>
                            <p>Kills: <span class="bold lightgreen">{{$player->kills}}</span> <span class="right">Deaths: <span class="bold orangered">{{$player->deaths}}</span></span></p>
                            <p class="score bold text-center">Total Score: <span class="lightgreen">{{$player->score}}</span></p>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="content-block">
            <h2>Losing Team</h2>
            <div class="match-history">
                @foreach($players as $player)
                    @if($player->result == 'false')
                        <?php 
                        $playerName = Player::where('id', $player->player_id)->first(); 
                        ?>
                    <div class="match">
                        <img src="{{asset('images/'.$player->hero_name.'/icon.png')}}" class="hero-img" alt="{{$player->hero_name}}">
                        <p class="match-type">{{$player->type}}</p>
                        <p class="result loss">{{$playerName->username}}</p>
                        <p>as {{$player->hero_name}}</p>
                        <p class="time">{{date("m-d-Y", strtotime($player->date))}}</p>
                        <a href="/player/{{$playerName->username}}" class="expand">View player page</a>
                        <div class="match-stats-box" style="clear:both;">
                            <p>Damage Done: <span class="bold lightgreen">{{$player->damage_done}}</span> <span class="right">Damage Received: <span class="bold orangered">{{$player->damage_received}}</span></span></p>
                            <p>Healing Done: <span class="bold lightgreen">{{$player->healing_done}}</span> <span class="right">Healing Received: <span class="bold orangered">{{$player->healing_received}}</span></span></p>
                            <p>Disables Done: <span class="bold lightgreen">{{$player->disables_done}}</span> <span class="right">Disables Received: <span class="bold orangered">{{$player->disables_received}}</span></span></p>
                            <p>Kills: <span class="bold lightgreen">{{$player->kills}}</span> <span class="right">Deaths: <span class="bold orangered">{{$player->deaths}}</span></span></p>
                            <p class="score bold text-center">Total Score: <span class="orangered">{{$player->score}}</span></p>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection