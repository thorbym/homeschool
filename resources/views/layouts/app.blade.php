@extends('layouts.head')

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
            <div class="row justify-content-center" style="margin-bottom: 10px">
                <div class="col-md-1">

                </div>
                <div class="col-md-8">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ last(request()->segments()) == 'calendar' ? 'active' : '' }}" href="{{ route('calendar') }}">Scheduled events</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ last(request()->segments()) == 'list' ? 'active' : '' }}" href="{{ route('list') }}">Watch any time</a>
                        </li>
                        @if (Auth::check() && Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ last(request()->segments()) == 'categories' ? 'active' : '' }}" href="{{ url('categories') }}">Categories</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-3">
                </div>
            </div>
            @yield('content')
        </main>
    </div>
    <br />
    <br />
    <footer class="section footer-classic context-dark" id="footer" style="display: none; background: #2d3246;">
        <div class="container">
            <div>
                <br />
                <p style="color: white">
                    <small>
                        All rights reserved to TeachEm ©2020. You can click to read our <a href="{{ route('privacyPolicy') }}">privacy policy</a> and our <a href="{{ route('terms') }}">terms and conditions</a>.<br />
                        Want to add your event to our app? Or talk to us about advertising? Contact us on teachem.online2020@gmail.com<br />
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
            document.getElementById("footer").style.position = "relative";
            document.getElementById("footer").style.display = "block";
        });
    </script>