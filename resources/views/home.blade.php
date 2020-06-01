  @extends('layouts.head')

  <body style="padding-top: 4.4rem">

    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" style="height: 4.4rem">
      <a class="navbar-brand" href="#">
        <img height="40" src="{{ asset('img/logo_blue.png') }}">
      </a>
      <!--<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active py-2">
            <a class="nav-link" href="#">Watch live</a>
          </li>
          <li class="nav-item py-2">
            <a class="nav-link" href="#">Watch any time</a>
          </li>
          <li class="nav-item dropdown py-2">
            <a class="nav-link dropdown-toggle" href="http://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
      </div>-->
    </nav>

    <main role="main">

      <!-- Main jumbotron for a primary marketing message or call to action -->
      <div class="jumbotron" id="jumbotron" style="height: calc(100vh - 4.4rem); background-color: #A5E8FD; min-height: calc(100% - 4.4rem); min-height: calc(100vh - 4.4rem); display: flex; align-items: center; text-align: center">
        <div class="container">
          <p>
            <h1 class="display-4">Help for busy households</h1>
          </p>
          <p>
            <h4>TeachEm is your online directory of video-based educational content. Find perfectly suited live and pre-recorded content quickly.</h4>
          </p>
          <p><a class="btn btn-primary btn-lg" href="{{ route('calendar') }}" role="button">Find content &raquo;</a></p>
          <p><a class="btn btn-primary-outline btn-lg" href="#" onclick="scrollDiv()" role="button"><i class="fas fa-chevron-down"></i></a></p>
        </div>
      </div>

      <div class="container" id="details">
        <!-- Example row of columns -->
        <div class="row">
          <div class="col-md-4" style="text-align: center">
            <img src="{{ asset('img/002-expert.png') }}" style="max-width:50%; max-height:50%"><br /><br />
            <h2>Expert help</h2>
            <p>
              Benefit from the expertise of passionate and experienced professionals
            </p>
            <p><a class="btn btn-secondary" href="{{ route('calendar') }}" role="button">Find events &raquo;</a></p>
          </div>
          <div class="col-md-4" style="text-align: center">
            <img src="{{ asset('img/003-radio.png') }}" style="max-width:50%; max-height:50%"><br /><br />
            <h2>Live sessions</h2>
            <p>
              Know exactly when all your favourite sessions are happening, and tune in
            </p>
            <p><a class="btn btn-secondary" href="{{ route('calendar') }}" role="button">See schedule &raquo;</a></p>
          </div>
          <div class="col-md-4" style="text-align: center">
            <img src="{{ asset('img/004-past.png') }}" style="max-width:49%; max-height:49%"><br /><br />
            <h2>Catch up</h2>
            <p>
              Missed a session? Search through and catch up in your own time
            </p>
            <p><a class="btn btn-secondary" href="{{ route('list') }}" role="button">Start searching &raquo;</a></p>
          </div>
        </div>

        <hr>

      </div> <!-- /container -->

    </main>
    <br />
    <br />
    <footer class="section footer-classic context-dark" style="background: #2d3246; position: relative;">
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
  <script type="text/javascript">
    function scrollDiv() {
      var details = document.getElementById('details');
      var jumbotron = document.getElementById('jumbotron');
      window.scrollTo({
        top: details.offsetTop - (jumbotron.offsetTop + 20),
        behavior: "smooth"
      });
    }
  </script>