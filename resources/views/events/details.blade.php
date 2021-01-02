@php
    $nextEvent = $eventCalendar ? $eventCalendar : false;
@endphp

@extends('layouts.app')

@section('content')

@include('layouts.back')

@php
    $event->averageRating = $event->averageRating(true)[0];
    $event->timesRated = $event->countRating(true)[0];
@endphp
<style>
.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: center
}

.rating>input {
    display: none
}

.rating>label {
    position: relative;
    width: 1em;
    font-size: 2.5vw;
    color: #FFD600;
    cursor: pointer
}

.rating>label::before {
    content: "\2605";
    position: absolute;
    opacity: 0
}

.rating>label:hover:before,
.rating>label:hover~label:before {
    opacity: 1 !important
}

.rating>input:checked~label:before {
    opacity: 1
}

.rating:hover>input:checked~label:before {
    opacity: 0.4
}

</style>
<div class="container-fluid" style="padding: 20px 40px 20px 40px;">
    <div class="row">
        <div class="col-lg-4 order-1">
            <div class="image_selected">
                @if ($event->image_file_id)
                    <img width="100%" style="border-radius: 10px" src="{{ url('storage/'.$event->image_file_id) }}" alt="no image">
                @elseif ($event->video_link)
                    <div style="position: relative; display: block; width: 90%; height: 0; margin: auto; padding: 0% 0% 56.25%; overflow: hidden;">
                        <iframe style="position: absolute; top: 0; bottom: 0; left: 0; width: 100%; height: 100%; border: 0;" src="https://www.youtube.com/embed/{{ $event->video_link }}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </div>
                @elseif ($event->image_link)
                    <img width="100%" style="border-radius: 10px" src="{{ $event->image_link }}" alt="no image">
                @else
                    @if (strpos($event->description, '//www.youtube.com/embed/') !== false)
                        @php $firstPos = strpos($event->description, '//www.youtube.com/embed/') + 24 @endphp
                        @php $lastPos = strpos($event->description, '"', $firstPos) @endphp
                        @php $youtubeLink = substr($event->description, $firstPos, ($lastPos - $firstPos)) @endphp
                            <div style="position: relative; display: block; width: 90%; height: 0; margin: auto; padding: 0% 0% 56.25%; overflow: hidden;">
                                <iframe style="position: absolute; top: 0; bottom: 0; left: 0; width: 100%; height: 100%; border: 0;" src="https://www.youtube.com/embed/{{ $youtubeLink }}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                            </div>
                    @elseif (strpos($event->description, 'img src=') !== false)
                        @php $firstPos = strpos($event->description, 'img src=') + 9 @endphp
                        @php $lastPos = strpos($event->description, '"', $firstPos) @endphp
                        @php $pictureLink = substr($event->description, $firstPos, ($lastPos - $firstPos)) @endphp
                        <img width="100%" style="border-radius: 10px" src="{{ $pictureLink }}" alt="no image">
                    @else
                        <img width="100%" style="border-radius: 10px" src="{{ asset('img/no_image_placeholder.jpg') }}" alt="no image">
                    @endif
                @endif
            </div>
        </div>
        <div class="col-lg-7 order-3" style="margin-left: 20px; margin-top: 20px">

            <!-- event title -->
            <h3>{{ $event->title }}</h3>
            {!! Helper::getRatingStars($event->averageRating) !!}&nbsp<a href="#reviews"><small style="color: #969595">({{ $event->timesRated }} reviews)</small></a>
            <p style="margin-top: 10px">
                @if ($event->favourite)
                    <a href="#" class="btn btn-link" id="saveFavourite">
                        <i class="fas fa-heart align-middle" style="color: red; margin-right: 5px"></i>Saved as favourite
                @else
                    <a href="#" class="btn btn-link" id="saveFavourite">
                        <i class="far fa-heart align-middle" style="margin-right: 5px"></i>Save to favourites
                @endif
                </a>
            </p>

            <hr>

            <!-- event description -->
            <p>{!! $event->description !!}</p>

            <!-- event summary section -->
            <p>
                <ul>
                    <li>Suitable for {{ $event->minimum_age }} to {{ $event->maximum_age }} years old</li>
                    @if ($event->requires_supervision)
                        <li>Supervision required</li>
                    @endif
                    @if (!$event->free_content)
                        <li>Content requires payment</li>
                    @endif
                </ul>
            </p>
            <hr>

            <!-- watch links -->
            <h5>Live streams</h5>

            @if ($nextEvent)
                <p class="clickthrough">
                    @if ($event->live_youtube_link)
                        <a href="{{ $event->live_youtube_link }}" data-platform="live_youtube_link" target="_blank" class="btn btn-sm" style="background-color: red; color: white">
                            <span class="align-middle"><i class="fab fa-youtube align-middle"></i>&nbsp; YouTube</span>
                        </a>&nbsp&nbsp&nbsp
                    @endif
                    @if ($event->live_facebook_link)
                        <a href="{{ $event->live_facebook_link }}" data-platform="live_facebook_link" target="_blank" class="btn btn-sm" style="background-color: #3b5998; color: white">
                            <span class="align-middle"><i class="fab fa-facebook align-middle"></i>&nbsp; Facebook</span>
                        </a>&nbsp&nbsp&nbsp
                    @endif
                    @if ($event->live_instagram_link)
                        <a href="{{ $event->live_instagram_link }}" data-platform="live_instagram_link" target="_blank" class="btn btn-sm" style="background-color: #517fa4; color: white">
                            <span class="align-middle"><i class="fab fa-instagram align-middle"></i>&nbsp; Instagram</span>
                        </a>&nbsp&nbsp&nbsp
                    @endif
                    @if ($event->live_web_link)
                        <a href="{{ $event->live_web_link }}" data-platform="live_web_link" target="_blank" class="btn btn-sm" style="background-color: blue; color: white;">
                            <span class="align-middle"><i class="fas fa-globe align-middle"></i>&nbsp; Web</span>
                        </a>&nbsp&nbsp&nbsp
                    @endif
                </p>
                <p>
                    <small>
                        <select id="saveCalendarEvent" onchange="saveCalendarEvent(this)" style="border:0px; outline:0px;">
                            <option value="">Save to your calendar &nbsp;</option>
                            <option value="{{ $linkToCalendar->google }}">Google</option>
                            <option value="{{ $linkToCalendar->yahoo }}">Yahoo</option>
                            <option value="{{ $linkToCalendar->webOutlook }}">Outlook (web)</option>
                            <option value="{{ $linkToCalendar->ics }}">Outlook (ics file)</option>
                        </select>
                    </small>
                </p>
            @else
                <p>
                    <small><i>This event doesn't stream live</i></small>
                </p>
            @endif

            <h5>Watch anytime</h5>
            <p class="clickthrough">
                @if ($event->youtube_link)
                    <a href="{{ $event->youtube_link }}" data-platform="youtube_link" target="_blank" class="btn btn-sm" style="background-color: red; color: white">
                        <span class="align-middle"><i class="fab fa-youtube align-middle"></i>&nbsp; YouTube</span>
                    </a>&nbsp&nbsp&nbsp
                @endif
                @if ($event->facebook_link)
                    <a href="{{ $event->facebook_link }}" data-platform="facebook_link" target="_blank" class="btn btn-sm" style="background-color: #3b5998; color: white">
                        <span class="align-middle"><i class="fab fa-facebook align-middle"></i>&nbsp; Facebook</span>
                    </a>&nbsp&nbsp&nbsp
                @endif
                @if ($event->instagram_link)
                    <a href="{{ $event->instagram_link }}" data-platform="instagram_link" target="_blank" class="btn btn-sm" style="background-color: #517fa4; color: white">
                        <span class="align-middle"><i class="fab fa-instagram align-middle"></i>&nbsp; Instagram</span>
                    </a>&nbsp&nbsp&nbsp
                @endif
                @if ($event->web_link)
                    <a href="{{ $event->web_link }}" data-platform="web_link" target="_blank" class="btn btn-sm" style="background-color: blue; color: white;">
                        <span class="align-middle"><i class="fas fa-globe align-middle"></i>&nbsp; Web</span>
                    </a>&nbsp&nbsp&nbsp
                @endif
            </p>

            <!--
                <div id="loginInfo" class="row" style="display: none">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-8">
                        <i class="fas fa-exclamation-circle fa-lg align-middle" style="color: red"></i>&nbsp To save your favourites, you will need to <a href="{{ route('register') }}">register</a>. If you have already registered, <a href="{{ route('login') }}">please login</a>.
                    </div>
                </div>
                <div style="position: absolute; bottom: -40; right: 20">
                    <a id="favourite" href="#">
                        @if ($event->favourite)
                            <i class="fas fa-heart fa-2x" id="{{ $event->favourite->id }}" style="color: red"></i>
                        @else
                            <i class="far fa-heart fa-2x" style="color: gray"></i>
                        @endif
                    </a>
                </div>
            -->

        </div>
    </div>
    <br />
    <hr>
    <br />
    <div class="row">
        <div class="col-md-3">
            <h4>Review info</h4>
            <p>
                {!! Helper::getRatingStars($event->averageRating, 'fa-lg') !!}
                &nbsp&nbsp {{ round($event->averageRating,2) }} out of 5
            </p>
            <p>
                {{ $event->timesRated }} reviews
            </p>
            <p>
                @foreach (range(5, 1) as $star)
                    <div class="row" style="margin-bottom: 5px">
                        <div class="col-md-3">
                            <small>{{ $star }} star</small>
                        </div>
                        <div class="col-md-7">
                            <div class="progress" style="height: 20px">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <small>(0%)</small>
                        </div>
                    </div>
                @endforeach
            </p>
            <p><a href="#addReview" id="rateOrReview">Rate or review this event!</a></p>
            <form action="{{ url('/review/' . $event->id) }}" method="POST">
                @csrf
                <div id="addReview" style="display: none">
                    <div class="form-group row pull-left">
                        <div class="col-sm-12">
                            <select required class="form-control" name="rating" id="rating" onchange="$('#review').show(200)">
                                <option disabled selected>Rate this event!</option>
                                <option value="5">5 stars (excellent)</option>
                                <option value="4">4 stars</option>
                                <option value="3">3 stars</option>
                                <option value="2">2 stars</option>
                                <option value="1">1 star (not good)</option>
                            </select>
                        </div>
                    </div>
                    <div id="review" style="display: none">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <textarea class="form-control" rows="6" name="review" id="review" placeholder="Optional review"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="title" id="title" placeholder="Optional review title">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Your review will need to be approved before it becomes visible. Continue?')">Save your review</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-8 offset-md-1" id="reviews">
            <h4>Recent reviews</h4>
            @if ($event->timesRated < 1)
                <p>
                    There are currently no reviews for this event
                </p>
            @else
                @php
                    $reviews = $event->getRecentRatings($event->id);
                @endphp
                @foreach ($reviews as $review)
                    <p>
                        {!! Helper::getRatingStars($review->rating) !!} &nbsp&nbsp <span class="font-weight-bold">{{ $review->title }}</span>
                    </p>
                    <p>
                        <small>Review by {{ $review->author->name }} on {{ date('F d Y', strtotime($review->created_at)) }}</small>
                    </p>
                    <p>
                        {{ $review->body }}
                    </p>
                    <br />
                @endforeach
            @endif
        </div>
        </div>
    </div>
</div>
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>

@endsection

<script type="text/javascript" defer>

    var user_id = @json(Auth::check() ? Auth::user()->id : 0);
    var event_id = @json($event->id);
    var timezone = @json($event && $event->timezone ? $event->timezone : 0);

    document.addEventListener('DOMContentLoaded', function() {

        $('#liveEvent').on('click', function(e){
            $('#liveEventInfo').toggle(300);
            if ($(e.currentTarget).not(':checked')) {
                $('[id^=live_]').val('');
                $('#days_of_week option').prop('selected', false);
                $('#start_time').val('');
                $('#end_time').val('');
            }
        })

        if (!timezone) {
            $('#timezone').val(Intl.DateTimeFormat().resolvedOptions().timeZone);
        } else {
            $('#timezone').val(timezone);
        }

        $('.timepicker').timepicker({
            timeFormat: 'HH:mm',
            minTime: '07:00',
            maxTime: '21:00',
            interval: 30,
            zindex: 9999999
        });

        $('#saveFavourite').on('click', function(e){
            if (user_id) {
                var heart = $(e.currentTarget).children();
                if (heart.hasClass('fas')) {
                    axios.delete('/api/favourite/' + event_id)
                        .then(function (response) {
                            heart.toggleClass('fas far').css('color', 'gray');
                            var newHtml = heart.parent().html().replace('Saved as favourite', 'Save to favourites');
                            heart.parent().html(newHtml).blur();
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                } else {
                    axios.post('/api/favourite', {
                            event_id: event_id
                        })
                        .then(function (response) {
                            heart.toggleClass('fas far').css('color', 'red').blur();
                            var newHtml = heart.parent().html().replace('Save to favourites', 'Saved as favourite');
                            heart.parent().html(newHtml).blur();
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                }
            } else {
                var currentUrl = @json(base64_encode(url()->current()));
                axios.get('/api/loginWarning/show/fromFavourite/' + currentUrl)
                    .then(function (response) {
                        $('#loginModal').html(response.data).modal();
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            }
            return false;
        });

        $('#rateOrReview').on('click', function(e){
            if (user_id) {
                $('#addReview').toggle('300');
            } else {
                var currentUrl = @json(base64_encode(url()->current() . '#reviews'));
                axios.get('/api/loginWarning/show/fromRating/' + currentUrl)
                    .then(function (response) {
                        $('#loginModal').html(response.data).modal();
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            }
            return false;
        });

        $('.clickthrough').on('click', function(e){
            var clickthrough = $(e.currentTarget).children();
            var href = clickthrough.attr('href');
            var platform = clickthrough.data('platform');
            var date = new Date();
            var clicked_time = date.toString();
            axios.post('/api/clickthrough', {
                    event_id: event_id,
                    user_id: user_id,
                    clicked_link: btoa(href),
                    platform: platform,
                    clicked_time: clicked_time,
                    clicked_timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
                })
                .then(function (response) {
                    // done
                })
                .catch(function (error) {
                    console.log(error);
            });
        });

    });

</script>