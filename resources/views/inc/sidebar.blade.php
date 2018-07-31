<?php
use App\Hero;
use App\User;
use App\Auth;
use App\Build;
use App\Stream;
?>
<div class="sidebar-item rank-lookup">

        <div class="form-group">
            {{Form::label('playerName', 'Player Rank Lookup')}}
            {{Form::text('playerName', '', ['class' => 'form-control', 'placeholder' => 'Player Name'])}}
        </div>
        {{Form::button('Search', ['class' => 'btn btn-primary', 'onclick' => 'lookupRedirect()'])}}

</div>

@yield('sidebar')

<!--<div class="sidebar-item popular-builds">
    <h3 class="sidebar-title">Popular Builds</h3>
    <?php 
    $builds = Build::orderBy('views', 'desc')->paginate(5);
    ?>
    @if(count($builds) > 0) 
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
                        <small>Build by {{$username}} on {{$build->created_at}}</small>
                        <small>Views: {{$build->views}}</small>
                    </div>
                </div>
            </div>
        @endforeach
        
    @else
        <p>No builds Found</p>
    @endif
</div> -->

<div class="sidebar-item active-streams">
    <h3 class="sidebar-title">Featured Streams</h3>
    <?php $streams = Stream::orderBy('updated_at', 'desc')->take(5)->get();  ?>

    @foreach($streams as $stream)
        <div class="stream">
            <a href="http://twitch.tv/{{$stream['username']}}" class="bold">{{$stream['title']}}</a>
            <p>{{$stream['username']}} - {{$stream['viewers']}} viewers</p>
        </div>
    @endforeach
</div>

<div class="sidebar-ad">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Battlerites.net Sidebar Ad -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:300px;height:600px"
             data-ad-client="ca-pub-9625405497377874"
             data-ad-slot="7799422332"></ins>
        <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
</div>

<script type="text/javascript">
    function lookupRedirect() {
    window.location.href ="http://battlerites.net/player/" + $('#playerName').val();
}
</script>