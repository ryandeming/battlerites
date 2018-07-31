@extends('layouts.app')

@section('content')   
    @if(count($post) > 0) 
        <div class="post well">
            <h1 class="page-title">{{$post->title}}</h1>
            <p>{!!$post->body!!}</p>
        </div>
    @else
        <p>Post not found</p>
    @endif
@endsection