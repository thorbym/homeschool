@extends('layouts.head')

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('home') }}">
                    {{ config('app.name', 'Laravel') }}
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
            </div>
        </nav>

        <main class="py-4">
            <div class="row justify-content-center">
                <div class="col-md-11">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ last(request()->segments()) == 'calendar' ? 'active' : '' }}" href="{{ route('calendar') }}">Watch live</a>
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
            </div>
            <br />
            @yield('content')
        </main>
    </div>
    <br />
    <br />
    <footer class="section footer-classic context-dark" style="background: #2d3246;">
        <div class="container">
            <div>
                <br />
                <br />
                <p style="color: white">
                    All rights reserved to TeachEm ©2020. You can click to read our <a href="{{ route('privacyPolicy') }}">privacy policy</a> and our <a href="{{ route('terms') }}">terms and conditions</a>.
                </p>
                <br />
            </div>
        </div>
    </footer>
</body>
</html>