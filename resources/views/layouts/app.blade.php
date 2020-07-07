<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-168152835-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-168152835-1');
    </script>

    <!-- stop the Flash Of Unstyled Content -->
    <style>html{visibility: hidden;opacity:0;}</style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- favicon -->
    <link rel="icon" href="{{ URL::asset('teachem-favicon-32.png') }}" type="image/x-icon"/>

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md fixed-top navbar-dark bg-dark shadow-sm" style="min-height: 4.4rem">
                <a class="navbar-brand" href="{{ url('home') }}">
                <div class="d-none d-sm-block">
                    <img height="40" src="{{ asset('img/logo_blue.png') }}">
                </div>
                <div class="d-block d-sm-none">
                    <img height="40" src="{{ asset('img/icon_blue.png') }}">
                </div>
              </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
        </nav>

        <main class="py-4" style="margin-top: 3.8rem">
            @yield('content')
        </main>
        @if (last(request()->segments()) == 'calendar' || last(request()->segments()) == 'list')
            <a href="#">
                <div style='position: fixed; right: 20px; bottom: 20px; z-index: 9999999'>
                    <span class="fa-stack fa-3x">
                        <i class="fa fa-circle fa-stack-2x" style="color: green"></i>
                        <i class="fa fa-plus fa-stack-1x fa-inverse"></i>
                    </span>
                </div>
            </a>
        @endif
    </div>
    <br />
    <br />
    <footer class="section footer-classic context-dark" id="footer" style="display: none; background: #2d3246; min-height: 120px;">
        <div style="margin-bottom: 0; padding-bottom: 0; padding-left: 40px; padding-right: 40px">
            <div>
                <br />
                <p style="color: white">
                    <small>
                        All rights reserved to TeachEm ©2020 in the UK. You can click to read our <a href="{{ route('privacyPolicy') }}">privacy policy</a> and our <a href="{{ route('terms') }}">terms and conditions</a>.<br />
                        Want to add your event to our app? Or talk to us about advertising? Contact us on teachem.online2020@gmail.com, or via our twitter &nbsp;<a href="https://twitter.com/teachem2020"><i class="fab fa-twitter fa-lg"></i></a><br />
                        Icons made by <a href="https://www.flaticon.com/authors/freepik" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a>
                    </small>
                </p>
                <br />
            </div>
        </div>
    </footer>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.onload = function(){
            var el = $('html');
            var height = $('#footer').height();
            var bottomOfHtml = el.position().top + el.offset().top + el.outerHeight(true);
            var bottomOfViewport = (window.scrollY + window.innerHeight - 120);
            var bottom = bottomOfHtml > bottomOfViewport ? bottomOfHtml : bottomOfViewport;
            document.getElementById("footer").style.top = bottom + "px";
            document.getElementById("footer").style.display = "block";
            $('#footer').css('bottom', '');
        }
    });
</script>