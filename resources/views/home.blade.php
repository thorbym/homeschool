@extends('layouts.app')

@section('content')

@include('layouts.navbar')
<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron" id="jumbotron" style="height: calc(100vh - 4.4rem); background-color: #A5E8FD; min-height: calc(100% - 4.4rem); min-height: calc(100vh - 4.4rem); display: flex; align-items: center; text-align: center">
	<div class="container">
		<p>
			<h1 class="display-4">Help for busy households</h1>
		</p>
		<p>
			<h4>TeachEm is your online directory of video-based educational content. Find perfectly suited live and pre-recorded content quickly.</h4>
		</p>
		<p>
			<a class="btn btn-primary btn-lg" href="{{ route('calendarQuickStart') }}" role="button">Quick start &raquo;</a>
		</p>
		<p>
			<a href="{{ route('calendar') }}"><small>Just show me all the events</small></a>
		</p>
		<p>
			<a class="btn btn-primary-outline btn-lg" href="#" onclick="scrollDiv()" role="button"><i class="fas fa-chevron-down"></i></a>
		</p>
	</div>
</div>

<div class="container" id="details">
	<!-- Example row of columns -->
	<div class="row">
		<div class="col-md-4" style="text-align: center">
			<img src="{{ asset('img/002-expert.png') }}" style="max-width:50%; max-height:50%"><br /><br />
			<h2>Expert help</h2>
			<p>Benefit from the expertise of passionate and experienced professionals</p>
			<p>
				<a class="btn btn-secondary" href="{{ route('calendar') }}" role="button">Find events &raquo;</a>
			</p>
		</div>
		<div class="col-md-4" style="text-align: center">
			<img src="{{ asset('img/003-radio.png') }}" style="max-width:50%; max-height:50%"><br /><br />
			<h2>Live sessions</h2>
			<p>
				Know exactly when all your favourite sessions are happening, and tune in
			</p>
			<p>
				<a class="btn btn-secondary" href="{{ route('calendar') }}" role="button">See schedule &raquo;</a>
			</p>
		</div>
		<div class="col-md-4" style="text-align: center">
			<img src="{{ asset('img/004-past.png') }}" style="max-width:49%; max-height:49%"><br /><br />
			<h2>Catch up</h2>
			<p>
				Missed a session? Search through and catch up in your own time
			</p>
			<p>
				<a class="btn btn-secondary" href="{{ route('list') }}" role="button">Start searching &raquo;</a>
			</p>
		</div>
	</div>

	<hr>

</div> <!-- /container -->

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