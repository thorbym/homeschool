@php
    $nextEvent = $eventCalendar ? $eventCalendar : false;
@endphp

<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header gray">
            <h4 class="modal-title">{{ $event->title }}</h4>
            <button type="button" class="close align-middle" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fas fa-times fa-lg align-middle" style="color: gray"></i></span>
            </button>
        </div>
        <div class="modal-body">
            <div style="position: relative;">
                <h5><u>Description</u></h5>
                <p>{!! $event->description !!}</p>
                <p>
                    Information:
                    <ul>
                        <li>Suitable for {{ $event->minimum_age }} to {{ $event->maximum_age }} years old</li>
                        @if ($event->dfe_approved)
                            <li><i class="fas fa-graduation-cap" style="color: green"></i> &nbsp DfE Approved</li>
                        @endif
                        @if ($event->requires_supervision)
                            <li><i class="fas fa-binoculars fa-lg" style="color: orange"></i> &nbsp
                            Supervision required</li>
                        @endif
                        @if (!$event->free_content)
                            <li>&nbsp<i class="fas fa-dollar-sign fa-lg" style="color: red"></i> &nbsp&nbsp
                            Content requires payment</li>
                        @endif
                    </ul>
                </p>
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
            </div>
            @if ($nextEvent)
                <br />
                <hr>
                <h5><u>Live event</u></h5><br />
                <p id="eventTiming"></p>
                <p>
                    @if ($event->live_youtube_link)
                        <a href="{{ $event->live_youtube_link }}" target="_blank" class="btn btn-sm" style="background-color: red; color: white">
                            <span class="align-middle"><i class="fab fa-youtube fa-2x align-middle"></i>&nbsp; YouTube</span>
                        </a>&nbsp
                    @endif
                    @if ($event->live_facebook_link)
                        <a href="{{ $event->live_facebook_link }}" target="_blank" class="btn btn-sm" style="background-color: #3b5998; color: white">
                            <span class="align-middle"><i class="fab fa-facebook fa-2x align-middle"></i>&nbsp; Facebook</span>
                        </a>&nbsp
                    @endif
                    @if ($event->live_instagram_link)
                        <a href="{{ $event->live_instagram_link }}" target="_blank" class="btn btn-sm" style="background-color: #517fa4; color: white">
                            <span class="align-middle"><i class="fab fa-instagram fa-2x align-middle"></i>&nbsp; Instagram</span>
                        </a>&nbsp
                    @endif
                    @if ($event->live_web_link)
                        <a href="{{ $event->live_web_link }}" target="_blank" class="btn btn-sm" style="background-color: blue; color: white;">
                            <span class="align-middle"><i class="fas fa-globe fa-2x align-middle"></i>&nbsp; Web</span>
                        </a>&nbsp
                    @endif
                </p>
            @endif
            <br />
            <hr>
            <h5><u>Watch anytime</u></h5><br />
            <p>
                @if ($event->youtube_link)
                    <a href="{{ $event->youtube_link }}" target="_blank" class="btn btn-sm" style="background-color: red; color: white">
                        <span class="align-middle"><i class="fab fa-youtube fa-2x align-middle"></i>&nbsp; YouTube</span>
                    </a>&nbsp
                @endif
                @if ($event->facebook_link)
                    <a href="{{ $event->facebook_link }}" target="_blank" class="btn btn-sm" style="background-color: #3b5998; color: white">
                        <span class="align-middle"><i class="fab fa-facebook fa-2x align-middle"></i>&nbsp; Facebook</span>
                    </a>&nbsp
                @endif
                @if ($event->instagram_link)
                    <a href="{{ $event->instagram_link }}" target="_blank" class="btn btn-sm" style="background-color: #517fa4; color: white">
                        <span class="align-middle"><i class="fab fa-instagram fa-2x align-middle"></i>&nbsp; Instagram</span>
                    </a>&nbsp
                @endif
                @if ($event->web_link)
                    <a href="{{ $event->web_link }}" target="_blank" class="btn btn-sm" style="background-color: blue; color: white;">
                        <span class="align-middle"><i class="fas fa-globe fa-2x align-middle"></i>&nbsp; Web</span>
                    </a>&nbsp
                @endif
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<script>

    var user_id = @json(Auth::check() ? Auth::user()->id : 0);
    var event_id = @json($event->id);
    var nextEvent = @json($nextEvent);
    var eventTimezone = @json($event->timezone);
    var fromCalendar = @json($fromCalendar);

    $(document).ready(function(){
        $('#favourite').on('click', function(e){
            if (user_id) {
                var heart = $(e.currentTarget).children();
                if (heart.hasClass('fas')) {
                    var id = heart.attr('id');
                    axios.delete('/api/favourite/' + id)
                        .then(function (response) {
                            heart.toggleClass('fas far').css('color', 'gray');
                            if ($('table tr#' + event_id + ' td.favouriteTd')) {
                                var tableIcon = $('table tr#' + event_id + ' td.favouriteTd');
                                tableIcon.attr('data-search', '0').children().children().toggleClass('fas far').css('color', 'gray').remove('id');
                            }
                            if (calendar) {
                                calendar.refetchEvents();
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                } else {
                    axios.post('/api/favourite', {
                            event_id: event_id,
                            user_id: user_id
                        })
                        .then(function (response) {
                            heart.toggleClass('fas far').css('color', 'red').attr('id', response.data.id);
                            if ($('table tr#' + event_id + ' td.favouriteTd')) {
                                var tableIcon = $('table tr#' + event_id + ' td.favouriteTd');
                                tableIcon.attr('data-search', '1').children().children().toggleClass('fas far').css('color', 'red').attr('id', response.data.id);
                            }
                            if (calendar) {
                                calendar.refetchEvents();
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                }
            } else {
                $('#loginInfo').toggle();
            }
        });
        if (nextEvent) {
            var userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            var eventStartDateTime = moment.tz(nextEvent.start, eventTimezone);
            var convertedStartDateTime = eventStartDateTime.clone().tz(userTimezone);
            if (fromCalendar) {
                $("#eventTiming").html('<i>' + convertedStartDateTime.format('ddd DD MMM Y HH:mm') + ' (' + convertedStartDateTime.fromNow() + ')</i>');
            } else {
                var diff = convertedStartDateTime.diff(moment());
                if (diff < 0) {
                    $("#eventTiming").html('<i><strong>** Event in progress! Finishes ' + moment.tz(nextEvent.end, eventTimezone).fromNow() + ' **</strong></i>');
                } else {
                    $("#eventTiming").html('<i>Next broadcasting ' + convertedStartDateTime.format('ddd DD MMM Y HH:mm') + ' (' + convertedStartDateTime.fromNow() + ')</i>');
                }
            }
        }

    })
    function saveCalendarEvent(e) {
        if (e.value) {
            window.open(e.value);
        }
        e.value = "";
    }
</script>