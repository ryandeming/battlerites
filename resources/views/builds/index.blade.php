@extends('layouts.app')
<?php
use App\Hero;
use App\User;
use App\Auth;
$heroes = Hero::all();
if(isset($hero)) {
    $heroName = $hero->name;
    $description = $heroName. ' builds for battlerite. Learn the optimal way to play '.$heroName.' to climb the leaderboards and improve your ranking.';
} else {
    $heroName = '';
    $description = 'Builds for battlerite. Learn the optimal way to play characters to climb the leaderboards and improve your ranking.';
}
?>
@section('title', $title)
@section('description', $description)
@section('content')
        <div class="content-block">
            <h2 class="page-title">Filter by Hero</h2>
            <div class="filter-list">
            @foreach($heroes as $hero) 
                <div class="hero-img">
                    <a href="/builds/{{$hero->name}}"><img src="{{asset('images/'.$hero->name.'/icon.png')}}"></a>
                </div>
            @endforeach
            </div>
        </div>
    <div class="content-block">
        <h1 class="page-title">Builds</h1>
        @if(count($builds) > 0) 
            <div class="builds-box">
                @foreach($builds as $build)
                <?php $selectedBattlerites = explode(', ', $build->build, 5);
                    $hero = Hero::where('id', $build->hero_id)->firstOrFail();
                    if($build->user_id != 0) {
                        $user = User::where('id', $build->user_id)->firstOrFail();
                        $username = $user->name;
                    } else {
                        $username = 'Anonymous';
                    }
                ?>
                    <div class="build-row">
                        <div class="build-content">
                            <div class="build-image">
                                    <a href="/builds/view/{{$build->id}}-{{$build->slug}}">
                                        <img src="{{asset('images/'.$hero->name.'/icon.png')}}">
                                    </a>
                            </div>
                            <div class="build-desc">
                                <h3><a href="/builds/view/{{$build->id}}-{{$build->slug}}">{{$build->title}}</a></h3>
                                <small>Build by {{$username}} on {{date("m-d-Y", strtotime($build->created_at))}}</small>
                                <small>{{$build->build}}</small>
                                <small><?php if($build->featured == 1) { echo '<span class="primary">FEATURED</span> | '; } ?>Views: {{$build->views}} | Score {{$build->score}}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            
                {{$builds->links()}}
            </div>
        </div>
            @else
                <p>No builds Found. <a href="/builds/create/{{$heroName}}">Would you like to create one?</a></p>
        </div>
            @endif
@endsection