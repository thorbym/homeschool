@extends('layouts.app')

@section('content')

<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron" id="jumbotron" style="height: 100vh; background-color: #A5E8FD; min-height: 100vh; display: flex; align-items: center; text-align: center; position: inherit;">
	<div class="container">
		<p>
			<h1 class="display-4" style="padding-top: 20px">Learn from the experts</h1>
		</p>
		<p>
			<h4>TeachEm is a growing community of educators, sharing their expertise through high quality live streamed and pre-recorded educational video content.</h4>
		</p>
		<p>
			<a class="btn btn-primary btn-lg" href="{{ route('listQuickStart') }}" role="button">Quick start &raquo;</a>
		</p>
		<p>
			<a href="{{ route('list') }}"><small>Just show me all the events</small></a>
		</p>
		<p>
			<a class="btn btn-primary-outline btn-lg" href="#" onclick="scrollDiv()" role="button"><i class="fas fa-chevron-down"></i></a>
		</p>
	</div>
</div>

<div style="width: 100%; text-align: center; font-size: 1.1em" id="details">
	<br />
	<div class="container">
		<h2><strong>TeachEm for homeschooling</strong></h2><br />
		<p>
			All over the world, more and more families are choosing to homeschool their children and will no doubt have already been overwhelmed by the sheer amount of online resources available.
		</p>
		<br />
		<h5>
		    <strong>At TeachEm, we aim to keep things simple.</strong>
		</h5>
		<br />
		<p>
			TeachEm is your online directory of educational video content. Whether you're looking for a session to join in with LIVE, or a new favourite YouTube channel to subscribe to, you'll find it on TeachEm.
		</p>
		<br />   
		<p>
			<i class="fas fa-check-square fa-lg" style="color: #A5E8FD"></i> &nbsp; Choose day/week view to see what's on and when
		</p>
		<p>
			<i class="fas fa-check-square fa-lg" style="color: #A5E8FD"></i> &nbsp; Use our filters to find the perfect content for you
		</p>
		<p>
			<i class="fas fa-check-square fa-lg" style="color: #A5E8FD"></i> &nbsp; Save your favourites to create your own timetable
		</p>
		<br />
		<br />
	</div>
</div>

<div style="background-color: #E8E8E8; width: 100%; text-align: center; font-size: 1.1em; padding-top: 30px">
	<div class="container">
		<br />
		<!-- Example row of columns -->
		<div class="row">
			<div class="col-md-4" style="text-align: center">
				<img src="{{ asset('img/002-expert.png') }}" style="max-width:50%; max-height:50%"><br /><br />
				<h2><strong>Expert help</strong></h2>
				<p>Benefit from the expertise of passionate and experienced professionals</p>
				<p>
					<a class="btn btn-secondary" href="{{ route('calendar') }}" role="button">Find events &raquo;</a>
				</p>
			</div>
			<div class="col-md-4" style="text-align: center">
				<img src="{{ asset('img/003-radio.png') }}" style="max-width:50%; max-height:50%"><br /><br />
				<h2><strong>Live sessions</strong></h2>
				<p>
					Know exactly when all your favourite sessions are happening, and tune in
				</p>
				<p>
					<a class="btn btn-secondary" href="{{ route('calendar') }}" role="button">See schedule &raquo;</a>
				</p>
			</div>
			<div class="col-md-4" style="text-align: center">
				<img src="{{ asset('img/004-past.png') }}" style="max-width:49%; max-height:49%"><br /><br />
				<h2><strong>Catch up</strong></h2>
				<p>
					Missed a session? Search through and catch up in your own time
				</p>
				<p>
					<a class="btn btn-secondary" href="{{ route('list') }}" role="button">Start searching &raquo;</a>
				</p>
			</div>
		</div>
		<br />
		<br />
	</div> <!-- /container -->
</div>

<div style="width: 100%; text-align: center; font-size: 1.1em">
	<br />
	<br />
	<div class="container" style="padding: 0px 90px 0px 90px">
		<h2><strong>TeachEm for Educators</strong></h2>
		<br />
		<p>
			From artists and musicians to scientists and mathematicians, teaching has moved beyond the classroom and people from around the world are now sharing their expertise in a bid to educate others.
		</p>
		<p>
			Whether you are a YouTuber looking for more subscribers, a teacher looking for more engagement with live-streamed lessons or simply somebody who wants to share their knowledge or expertise, we'd love to welcome you as a TeachEm Contributor.
		</p>
		<br />
		<p>
			<a class="btn btn-primary btn-lg" href="mailto:teachem2020@gmail.com" role="button">Contact Us</a>
		</p>
		<br />
		<p>
			<i class="fas fa-check-square fa-lg" style="color: #A5E8FD"></i> &nbsp; Share your content with audiences from around the world
		</p>
		<p>
			<i class="fas fa-check-square fa-lg" style="color: #A5E8FD"></i> &nbsp; Customise your own entries
		</p>
		<p>
			<i class="fas fa-check-square fa-lg" style="color: #A5E8FD"></i> &nbsp; Benefit from user ratings and reviews
		</p>
		<br />
	</div>
</div>
<div style="background-color: #E8E8E8; width: 100%; text-align: center; font-size: 1.1em; padding-top: 50px; padding-bottom: 30px">
		<p>
			<em>"This is a really useful tool, thank you. I find it a bit of a minefield with so much content out there <br>- an interactive 'What's-on Guide' is just great!"</em>
		</p>
</div>
<div style="width: 100%; text-align: center; font-size: 1.1em">
		<br />
		<p>
			You can find us on twitter here: &nbsp; <a href="https://twitter.com/teachem2020"><i class="fab fa-twitter fa-lg"></i></a><br />
			And on Facebook here: &nbsp; <a href="https://www.facebook.com/TeachEm2020/"><i class="fab fa-facebook fa-lg"></i></a>
		</p>
</div>

@endsection

<script type="text/javascript">

	function scrollDiv() {
		var details = document.getElementById('details');
		var jumbotron = document.getElementById('jumbotron');
		window.scrollTo({
			top: details.offsetTop - (jumbotron.offsetTop + 50),
			behavior: "smooth"
		});
	}

</script>