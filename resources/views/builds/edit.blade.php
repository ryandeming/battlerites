@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="builds-container">
                <h1 class="page-title">Edit Build: {{$build->title}}</h1>
                {!! Form::open(['action' => ['BuildsController@update', $build->id], 'method' => 'POST']) !!}
                        <div class="form-group">
                                
                                {{Form::label('title', 'Title')}}
                                {{Form::text('title', $build->title, ['class' => 'form-control', 'placeholder' => 'Title'])}}
                        </div>
                        <div class="form-group battlerites">
                                <label>Battlerites</label>
                                
                                <?php $lastHotkey = null; ?>
                                @foreach($hero->battlerites as $battlerite)
                                @if($lastHotkey != $battlerite->hotkey)
                                        <div style="clear:both;"></div>
                                @endif
                                {{Form::checkbox('build[]', $battlerite->name, null, ['id' => str_replace(" ", "-", $battlerite->name)])}}
                                <label for="{{str_replace(" ", "-", $battlerite->name)}}">
                                        <img src="{{asset('images/'.$hero->name.'/abilities/'.$battlerite->hotkey.'.png')}}" class="skill-img {{$battlerite->hotkey}} {{strtolower($battlerite->category)}}" alt="{{$hero->name}} Battlerite - {{$battlerite->name}}">
                                        <div class="tooltip">
                                                <h3>{{$battlerite->name}}</h3>
                                                <p>{{$battlerite->description}}</p>
                                        </div>
                                </label>
                                        <?php $lastHotkey = $battlerite->hotkey; ?>
                                       
             
                                @endforeach
                        </div>
                        <div class="form-group">
                                {{Form::label('body', 'Body')}}
                                {{Form::textarea('body', $build->body, ['class' => 'form-control', 'id' => 'article-ckeditor', 'placeholder' => 'Body Text'])}}
                        </div>
                                {{Form::text('hero_id', $hero->id, ['class' => 'hidden'])}}
                                {{Form::hidden('_method', 'PUT')}}
                        {{Form::submit('Submit', ['class' => 'btn btn-primary'])}}
                {!! Form::close() !!}
</div>

<script type="text/javascript">
$(function() {
    var battlerites = "{{$build->build}}";
    var checks = battlerites.split(', ');

    for (var i = 0, len = checks.length; i < len; i++) {
        checks[i] = checks[i].replace(" ", "-");
        $('#'+checks[i]).attr('checked', true);
        console.log(checks[i]);
    }
});
</script>
@endsection