@extends('layouts.app')

@section('content')

@include('layouts.navbar')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header">
                    @include('layouts.filter')
                </div>

                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div id="calendar">
                        <!-- calendar goes here -->
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
<div class="modal fade" id="quickStartModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
@endsection

<script>

    var isAdmin = @json(Auth::check() ? Auth::user()->isAdmin() : 0);
    var user_id = @json(Auth::check() ? Auth::user()->id : 0);
    var quickStart = @json(isset($data['quickStart']) ? 1 : 0);

    // get current date
    var lastVisitDate = @json(date('Y-m-d'));
    var currentDate = new Date();

    // remember it if A) they don't already have one or B) it doesn't match previous visit
    if (localStorage.getItem("lastVisitDate") == null || localStorage.getItem("lastVisitDate") != lastVisitDate) {
        localStorage.setItem("lastVisitDate", lastVisitDate);
        // if they either haven't visited the site before, or that day, move them onto today's calendar
        var d = currentDate;
        setScrollTime();
    } else {
        // if they have visited it today, check what date they were looking at and that will be our default
        if (localStorage.getItem("fcDefaultDate") !== null) {
            var d = new Date(localStorage.getItem("fcDefaultDate"));
            var scrollTime = currentDate.getHours() + ':00';
        } else {
            // if they haven't visited yet, they can default to today
            var d = currentDate;
            setScrollTime();
        }
    }

    // if a user has been booted to today, if it's after 9pm then kick them over to the next day
    function setScrollTime() {
        if (d.getHours() >= 21) {
            var scrollTime = '07:00';
            d.setDate(d.getDate() + 1);
        } else {
            var scrollTime = d.getHours() + ':00';
        }
    }

    var month = '' + (d.getMonth() + 1);
    var day = '' + d.getDate();
    var year = d.getFullYear();

    if (month.length < 2) {
        month = '0' + month;
    } 
    if (day.length < 2) {
        day = '0' + day;
    }
        
    var defaultDate = year + '-' + month + '-' + day;

    document.addEventListener('DOMContentLoaded', function() {

        var isMobile = (window.innerWidth >= 768) ? false : true;

        var isTouchDevice = function() {            
            var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
            var mq = function (query) {
                return window.matchMedia(query).matches;
            }
            if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
                return true;
            }
            // include the 'heartz' as a way to have a non matching MQ to help terminate the join
            // https://git.io/vznFH
            var query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join('');
            return mq(query);
        }

        // fullcalendar stuff
        var calendarEl = document.getElementById('calendar');

        var defaultView = (localStorage.getItem("fcDefaultView") !== null ? localStorage.getItem("fcDefaultView") : 'timeGridDay');
        window.calendar = new Calendar.Calendar(calendarEl, {
            defaultView: defaultView,
            defaultDate: defaultDate,
            minTime: "07:00:00",
            maxTime: "21:00:00",
            scrollTime: scrollTime,
            height: 600,
            displayEventTime: false,
            header: {
                left: isMobile ? 'prev' : 'prev,next',
                center: 'title',
                right: isMobile ? 'next' : 'timeGridDay,timeGridWeek'
            },
            plugins: [ bootstrapPlugin, timeGridPlugin, isAdmin ? interaction : '' ],
            themeSystem: 'bootstrap',
            events: function(fetchInfo, successCallback, failureCallback) {
                // add in the from and to dates, to get the correct events
                var filtersArr = {
                    from: fetchInfo.startStr,
                    to: fetchInfo.endStr
                }
                $(".filter-buttons button").each(function(){
                    if (!$(this).hasClass('disabled')) {
                        var mainFilter = $(this).attr('id');
                        if (mainFilter == 'showFavourites') {
                            filtersArr[mainFilter] = 'on';
                        } else {
                            filtersArr[mainFilter] = {};
                            $('.' + mainFilter + ' a').each(function(){
                                var id = $(this).attr('id');
                                id = id.replace(mainFilter + '_', '');
                                filtersArr[mainFilter][id] = $(this).hasClass('disabled') ? 'off' : 'on';
                            });
                        }
                    }
                });
                axios.get('/api/events/calendar/' + JSON.stringify(filtersArr))
                    .then(function (response) {
                        successCallback(response.data)
                    })
                    .catch(function (error) {
                        console.log(error);
                        failureCallback(err)
                    });
            },
            editable: isAdmin ? true : false,
            nowIndicator: true,
            slotEventOverlap: false,
            navLinks: true,
            locale: 'en-gb',
            firstDay: 1,
            allDaySlot: false,
            views: {
                timeGridWeek: {
                    columnHeaderFormat: { weekday: 'short', month: 'short', day: 'numeric', omitCommas: true }
                },
            },
            slotDuration: '00:15:00',
            slotLabelInterval: '01:00',
            slotLabelFormat: {
                hour: 'numeric',
                hourCycle: 'h12',
                omitZeroMinute: true,
                meridiem: 'short'
            },
            navLinkDayClick: function(date, jsEvent) {
                calendar.changeView('timeGridDay', date);
            },
            viewSkeletonRender: function(info) {
                localStorage.setItem("fcDefaultView", info.view.type);
            },
            datesRender: function(info) {
                localStorage.setItem("fcDefaultDate", info.view.currentStart);
            },
            eventRender: function(info) {

                // colour the event based on the category's colour
                $(info.el).css({
                    'background-color': info.event.extendedProps.colour,
                    'border-color': info.event.extendedProps.colour,
                    'color': info.event.extendedProps.font_colour == "black" ? "#212529" : "white",
                    'font-size': '0.9em',
                }).hover(function(){
                    $(this).css('cursor', 'pointer');
                });

                if (!isTouchDevice()) {
                    $(info.el).tooltip({
                        title: info.event.extendedProps.description.replace(/<(.|\n)*?>/g, '').substring(0, 40) + '... ',
                        placement: "top",
                        trigger: "hover",
                        container: "body"
                    });
                }

                // SEARCH BOX
                var searchTerm = $('#search').val();
                if (searchTerm !== '' && searchTerm !== undefined) {
                    if (info.event.title.toLowerCase().indexOf(searchTerm.toLowerCase()) < 0) {
                        return false;
                    }
                }
            },
            dateClick: function(info) {
                if (isAdmin) {
                    axios.get('/api/event/create')
                        .then(function (response) {
                            $('#eventModal').html(response.data).modal();
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                }
            },
            eventClick: function(info) {
                var calendarId = info.event.extendedProps.event_calendar_id;
                window.location = '/event/' + calendarId + '/showFromCalendar';
            }
        });

        calendar.render();

        // reset the url so people don't end up pressing back and getting the quick start again
        if (quickStart) {
            window.history.replaceState(null, null, '/calendar');
            axios.get('/api/quickStart/show')
                .then(function (response) {
                    $('#quickStartModal').html(response.data).modal();
                })
                .catch(function (error) {
                    console.log(error);
            });
        }

    });
</script>