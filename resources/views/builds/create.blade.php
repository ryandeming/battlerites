@extends('layouts.app')

@section('content')
<div class="builds-container">
        @if(count($heroes) > 0)
        <div class="content-block">
                <h1 class="page-title">Select Hero</h1>
                @foreach($heroes as $hero) 
                        <div class="hero-img">
                        <a href="create/{{$hero->name}}"><img src="{{asset('images/'.$hero->name.'/icon.png')}}"></a>
                        </div>
                @endforeach
        </div>
        @else 
        <div class="content-block">
                <h1 class="page-title">Create <span class="hero-name">{{$hero->name}}</span> Build</h1>
                {!! Form::open(['action' => 'BuildsController@store', 'method' => 'POST']) !!}
                        <div class="form-group">
                                
                                {{Form::label('title', 'Title')}}
                                {{Form::text('title', '', ['class' => 'form-control', 'placeholder' => 'Title'])}}
                        </div>
                        <div class="form-group battlerites">
                                <label>Battlerites</label>
                                
                                <?php $lastHotkey = null; ?>
                                @foreach($battlerites as $battlerite)
                                @if($lastHotkey != $battlerite->hotkey)
                                        <div style="clear:both;"></div>
                                @endif
                                {{Form::checkbox('build[]', $battlerite->name, null, ['id' => $battlerite->name])}}
                                <label for="{{$battlerite->name}}">
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
                                {{Form::textarea('body', '', ['class' => 'form-control', 'id' => 'article-ckeditor', 'placeholder' => 'Body Text'])}}
                        </div>
                                {{Form::text('hero_id', $hero->id, ['class' => 'hidden'])}}
                        {{Form::submit('Submit', ['class' => 'btn btn-primary'])}}
                {!! Form::close() !!}
        </div>
        @endif
</div>

<script type="text/javascript">
var $checkboxes = $('input[type=checkbox]');

$checkboxes.change(function () {
    if (this.checked) {
        if ($checkboxes.filter(':checked').length == 5) {
            $checkboxes.not(':checked').prop('disabled', true);
        }
    } else {
        $checkboxes.prop('disabled', false);
    }
});
</script>
@endsection