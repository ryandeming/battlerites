<?php
use App\Hero;
use App\User;
use App\Auth;
    $selectedBattlerites = explode(', ', $build->build, 6);
    if($build->user_id != 0) {
        $user = User::where('id', $build->user_id)->firstOrFail();
        $username = $user->name;
    } else {
        $username = 'Anonymous';
    }
    $description = $hero->name. ' build for battlerite. Learn the optimal way to play '.$hero->name.' to climb the leaderboards and improve your ranking.';

?>

@extends('layouts.app')
@section('title', $title)
@section('description', $description)
@section('content')

<div class="content-block">
    <div class="build-header">
        <img src="{{asset('images/'.$hero->name.'/icon.png')}}" class="hero-img" alt="{{$hero->name}}">
        <div class="build-title">
            <h1 class="page-title">{{$build->title}}</h1>
            <h2>Build by {{$username}} on {{date("m-d-Y", strtotime($build->created_at))}}</h2>
            <h3>Score: <span class="score">{{$build->score}}</span></h3>
        </div>
        <div class="rate">
            <div class="buttons">
                <span class="upvote" onclick="rate('{{$build->id}}', 'up')">Upvote</span> <br/>
                <span class="downvote" onclick="rate('{{$build->id}}', 'down')">Downvote</span>
            </div>
        </div>
    </div>
        <!-- SELECTED BATTLERITES -->
        @if(count($selectedBattlerites) > 0)
            @if(count($hero->battlerites) > 0)
                <div class="build-row view">
                @foreach($selectedBattlerites as $selectedBattlerite)
                    @foreach($hero->battlerites as $battlerite)
                        @if($selectedBattlerite == $battlerite->name)
                        <div class="build battlerites text-center">
                            <div class="build-img text-center">
                                <img src="{{asset('images/'.$hero->name.'/abilities/'.$battlerite->hotkey.'.png')}}" class="skill-img {{strtolower($battlerite->category)}}" alt="{{$hero->name}} Battlerite - {{$battlerite->name}}">
                            </div>
                            
                            <p>{{$battlerite->name}}</p>
                            <div class="tooltip text-center">
                                <h3>{{$battlerite->name}}</h3>
                                <p>{{$battlerite->description}}</p>
                            </div>
                        </div>
                        @endif
                    @endforeach
                @endforeach
                </div>
                </table>
            @endif
        @endif
        <!-- END SELECTED BATTLERITES -->
        <div class="build body">
            <h2>Author Advice</h2>
            {!!$build->body!!}
        </div>
        <!-- ABILITIES
        @if(count($hero->abilities) > 0)
            <table>
                <tr>
                    <th>Hotkey</th>
                    <th>Name</th>
                    <th>Description</th>
                </tr>
                @foreach($hero->abilities as $ability)
                        <td>{{$ability->hotkey}}</td>
                        <td>{{$ability->name}}</td>
                        <td>{{$ability->description}}</td>
                    </tr>
                @endforeach
            </table>
        @endif
        <!-- END ABILITIES -->
        </div>

        
@endsection


