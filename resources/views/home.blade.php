@extends('layouts.app')

@section('content')

@include('layouts.navbar')
<!-- Main jumbotron for a primary marketing message or call to action -->
<div class="jumbotron" id="jumbotron" style="height: calc(100vh - 4.4rem); background-color: #A5E8FD; min-height: calc(100% - 4.4rem); min-height: calc(100vh - 4.4rem); display: flex; align-items: center; text-align: center">
	<div class="container">
		<p>
			<h1 class="display-4">Homeschool help<br /> for busy households</h1>
		</p>
		<p>
			<h4>TeachEm is your online directory of video-based educational content. We'll help you quickly find perfectly suited live and pre-recorded content, for all ages, across a range of subjects.</h4>
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
	<br />
	<!-- Example row of columns -->
	<div class="row">
		<div class="col-md-4" style="text-align: center; font-size: 1.1em">
			<img src="{{ asset('img/002-expert.png') }}" style="max-width:50%; max-height:50%"><br /><br />
			<h2><strong>Expert help</strong></h2>
			<p>Benefit from the expertise of passionate and experienced professionals</p>
			<p>
				<a class="btn btn-secondary" href="{{ route('calendar') }}" role="button">Find events &raquo;</a>
			</p>
		</div>
		<div class="col-md-4" style="text-align: center; font-size: 1.1em">
			<img src="{{ asset('img/003-radio.png') }}" style="max-width:50%; max-height:50%"><br /><br />
			<h2><strong>Live sessions</strong></h2>
			<p>
				Know exactly when all your favourite sessions are happening, and tune in
			</p>
			<p>
				<a class="btn btn-secondary" href="{{ route('calendar') }}" role="button">See schedule &raquo;</a>
			</p>
		</div>
		<div class="col-md-4" style="text-align: center; font-size: 1.1em">
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

<div style="background-color: #E8E8E8; width: 100%; text-align: center; font-size: 1.1em">
	<br />
	<br />
	<div class="container" style="padding: 0px 70px 0px 70px">
		<h2><strong>TeachEm for homeschooling</strong></h2><br />
		<p>
			Balancing homeschooling whilst working from home is understandably difficult to manage, not to mention stressful. Meetings are scheduled and deadlines still need to be met, so engaging in focussed, educational activities with your children is not always possible. And sometimes you just need a break.
		</p>
		<br />
		<h5>
		    <strong>That’s where TeachEm comes in.</strong>
		</h5>
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
<div style="width: 100%; text-align: center; font-size: 1.1em">
	<br />
	<br />
	<div class="container" style="padding: 0px 90px 0px 90px">
		<h2><strong>TeachEm for school holidays</strong></h2>
		<br />
		<p>
			Keeping the children entertained over the school holidays is a daunting task. Holiday clubs are taking fewer children, and the weather can make outdoor play more difficult.
		</p>
		<p>
			There's a huge variety of educational content available online to support children’s learning and take some pressure off you, but not all working parents have time to find suitable sessions.
		</p>
		<br />
		<h5>
		    <strong>That’s where TeachEm comes in.</strong>
		</h5>
		<br />
		<p>
			<em>"This is a really useful tool, thank you. I find it a bit of a minefield with so much content out there <br>- an interactive 'TV Guide' is just great!"</em>
		</p>
	</div>
</div>


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