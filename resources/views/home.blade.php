  @extends('layouts.head')

  <body style="padding-top: 4.4rem">

    <nav class="navbar navbar-expand-md navbar-light fixed-top" style="background-color: #A5E8FD; height: 4.4rem">
      <a class="navbar-brand" href="#">TEACHEM!</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!--<div class="collapse navbar-collapse" id="navbarsExampleDefault">
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
      <div class="jumbotron" style="height: calc(100vh - 4.4rem); background-color: #A5E8FD; min-height: calc(100% - 4.4rem); min-height: calc(100vh - 4.4rem); display: flex; align-items: center; text-align: center">
        <div class="container">
          <p>
            <h1 class="display-4">Help for busy households</h1>
          </p>
          <p>
            <h4>TeachEm is your online directory of video-based educational content. Find perfectly suited live and pre-recorded content quickly.</h4>
          </p>
          <p><a class="btn btn-primary btn-lg" href="{{ route('calendar') }}" role="button">Search events &raquo;</a></p>
          <p><a class="btn btn-primary-outline btn-lg" href="#" role="button"><i class="fas fa-chevron-down"></i></a></p>
        </div>
      </div>

      <div class="container">
        <!-- Example row of columns -->
        <div class="row">
          <div class="col-md-4">
            <h2>Heading</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-secondary" href="#" role="button">View details &raquo;</a></p>
          </div>
          <div class="col-md-4">
            <h2>Heading</h2>
            <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
            <p><a class="btn btn-secondary" href="#" role="button">View details &raquo;</a></p>
          </div>
          <div class="col-md-4">
            <h2>Heading</h2>
            <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
            <p><a class="btn btn-secondary" href="#" role="button">View details &raquo;</a></p>
          </div>
        </div>

        <hr>

      </div> <!-- /container -->

    </main>
