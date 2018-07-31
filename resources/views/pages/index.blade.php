<?php 
use App\Hero;
use App\User;
use App\Auth;
use App\Build;
use App\Post;
$posts = Post::orderBy('created_at', 'desc')->paginate(5);
$heroes = Hero::all();
$recentBuilds = Build::orderBy('created_at', 'desc')->paginate(3);
$popularBuilds = Build::orderBy('views', 'desc')->paginate(3);
?>

@extends('layouts.app')

@section('title', 'For all your Battlerite needs')

@section('content')
<div class="hero content-block">
        <h3 class="bold primary-color mb-10">Find Builds</h3>
        @foreach($heroes as $hero) 
                <div class="hero-img">
                        <a href="/builds/{{$hero->name}}"><img src="{{asset('images/'.$hero->name.'/icon.png')}}"></a>
                </div>
        @endforeach                
</div>
<div class="row index">
        <div class="col-lg-6">
                <div class="builds-row content-block">
                        <h3 class="bold primary-color mb-10">Popular Builds</h3>
                        <div class="builds-box">
                                @foreach($popularBuilds as $build)
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
                                                        <small><?php if($build->featured == 1) { echo '<span class="primary">FEATURED</span> | '; } ?>Views: {{$build->views}} | Score {{$build->score}}</small>
                                                        </div>
                                                </div>
                                        </div>
                                @endforeach
                        </div>
                </div>
        </div>
        <div class="col-lg-6">
                <div class="content-block builds-row">
                        <h3 class="bold primary-color mb-10">Recent Builds</h3>
                        <div class="builds-box">
                                @foreach($recentBuilds as $build)
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
                                                        <small><?php if($build->featured == 1) { echo '<span class="primary">FEATURED</span> | '; } ?>Views: {{$build->views}} | Score {{$build->score}}</small>
                                                        </div>
                                                </div>
                                        </div>
                                @endforeach
                        </div>
                </div>
        </div>
</div>
<div class="row">
        <div class="col-xs-12">
                @if(count($posts) > 0) 
                        @foreach($posts as $post)
                        <div class="well post">
                                <h3 class="bold"><a href="/posts/{{$post->id}}">{{$post->title}}</a></h3>
                                <p>{!!$post->body!!}</p>
                        </div>
                        @endforeach
                @else
                        <p>No Posts Found</p>
                @endif
        </div>
</div>
@endsection
