<?php
use App\Hero;
use App\User;
use App\Auth;
?>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-115461054-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-115461054-1');
</script>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-9625405497377874",
    enable_page_level_ads: true
  });
</script>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Battlerites.net - @yield('title')</title>
    <meta name="description" content="@yield('description', 'Battlerite character builds, player ranking lookup, match history, and more.')">
    <meta name="og:image" content="{{ asset('images/favicon-128x128.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-32x32.png') }}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-16x16.png') }}" sizes="16x16" />
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
</head>
<body>
    @include('inc.navbar')
    <div class="container with-bg">
        <div class="row">
            <div class="main-content col-md-8 col-sm-12 col-xs-12">
                @include('inc.messages')
                @yield('content')
                <div style="margin: 0 auto 20px auto; text-align: center;">
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <!-- Battlerites.net Header Ad -->
                    <ins class="adsbygoogle"
                        style="display:inline-block;width:728px;height:90px; margin: 0 auto;"
                        data-ad-client="ca-pub-9625405497377874"
                        data-ad-slot="5009607465"></ins>
                    <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
            </div>
            <div class="sidebar col-md-4 col-sm-12 col-xs-12">
                @include('inc.sidebar')
            </div>
        </div>
     </div>


    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'article-ckeditor' );
    </script>
    <script type="text/javascript">
        function rate(build, rate) {
            var buildId = build;
            var rate = rate;
           window.location.href= "/rate/" + buildId + "/" + rate;
        }
    </script>
</body>
</html>
