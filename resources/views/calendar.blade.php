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
    var d = new Date();
    if (d.getHours() >= 21) {
        var scrollTime = '07:00';
        d.setDate(d.getDate() + 1);
    } else {
        var scrollTime = d.getHours() + ':00';
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

        function mobileCheck() {
            if (window.innerWidth >= 768 ) {
                return false;
            } else {
                return true;
            }
        };

        // fullcalendar stuff
        var calendarEl = document.getElementById('calendar');

        window.calendar = new Calendar.Calendar(calendarEl, {
            defaultView: 'timeGridDay',
            defaultDate: defaultDate,
            minTime: "07:00:00",
            maxTime: "21:00:00",
            scrollTime: scrollTime,
            height: 600,
            displayEventTime: false,
            header: {
                left: mobileCheck() ? 'prev' : 'prev,next',
                center: 'title',
                right: mobileCheck() ? 'next' : 'timeGridDay,timeGridWeek'
            },
            plugins: [ bootstrapPlugin, timeGridPlugin, isAdmin ? interaction : '' ],
            themeSystem: 'bootstrap',
            events: function(fetchInfo, successCallback, failureCallback) {
                var filtersArr = {}
                $(".filter-buttons button").each(function(){
                    if (!$(this).hasClass('disabled')) {
                        var mainFilter = $(this).attr('id');
                        if (mainFilter == 'showFavourites') {
                            filtersArr[mainFilter] = 'on';
                        } else {
                            filtersArr[mainFilter] = {};
                            $('.' + mainFilter + ' a').each(function(){
                                var id = $(this).attr('id');
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
            eventRender: function(info) {

                // colour the event based on the category's colour
                $(info.el).css({
                    'background-color': info.event.extendedProps.colour,
                    'border-color': info.event.extendedProps.colour,
                    'color': info.event.extendedProps.font_colour == "black" ? "#212529" : "white",
                    'font-size': '0.9em',
                }).hover(function(){
                    $(this).css('cursor', 'pointer');
                }).tooltip({
                    title: info.event.extendedProps.description.substring(0, 40) + '... ',
                    placement: "top",
                    trigger: "hover",
                    container: "body"
                });

                // SEARCH BOX
                var searchTerm = $('#search').val();
                if (searchTerm !== '') {
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
                var id = info.event.id;
                if (isAdmin) {
                    // admin can edit the events, so get event edit form
                    axios.get('/api/event/' + id + '/edit')
                        .then(function (response) {
                            $('#eventModal').html(response.data).modal();
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                } else {
                    // otherwise it is normal user, so show the "details" modal
                    axios.get('/api/event/' + id + '/show')
                        .then(function (response) {
                            $('#eventModal').html(response.data).modal();
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                }
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