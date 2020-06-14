@extends('layouts.app')

@section('content')

@include('layouts.navbar')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="filter-buttons form-inline">
                        <button type="button" class="btn btn-outline-secondary disabled" id="subjectFilter">
                            Subject 
                            <i class="fas fa-caret-down"></i>
                        </button>&nbsp&nbsp
                        <button type="button" class="btn btn-outline-secondary disabled" id="ageFilter">
                            Age 
                            <i class="fas fa-caret-down"></i>
                        </button>&nbsp&nbsp
                        <button type="button" class="btn btn-outline-secondary disabled" id="otherFilters">
                            Other 
                            <i class="fas fa-caret-down"></i>
                        </button>&nbsp&nbsp
                        <button type="button" class="btn btn-outline-secondary disabled" id="showFavourites">
                            &nbsp<i class="far fa-heart"></i>&nbsp
                        </button>&nbsp&nbsp
                        <div class="d-none d-sm-block">
                            <div class="input-group mb-3" style="margin-bottom: 0px !important">
                                <input type="text" id="search" class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary disabled" id="searchBtn" type="button"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="filters">
                        <div class="subjectFilter" style="display: none; padding: 15px 0px 10px 0px">
                            @foreach ($data['categories'] as $category)
                                <a href="#" id="{{ str_replace(' ', '', $category->category) }}" class="btn disabled" style="background-color: {{ $category->colour }}; color: {{ $category->font_colour }}; margin: 3px; pointer-events: auto">{{ $category->category }}</a>
                            @endforeach
                        </div>
                        <div class="ageFilter" style="display: none; padding: 15px 0px 10px 0px">
                            <a href="#" id="littleKids" class="btn btn-warning disabled" style="margin: 3px; pointer-events: auto">6 and under</a>
                            <a href="#" id="middleKids" class="btn disabled" style="margin: 3px; pointer-events: auto; background-color: orange">7 to 11</a>
                            <a href="#" id="bigKids" class="btn btn-info disabled" style="margin: 3px; pointer-events: auto">12 and older</a>
                        </div>
                        <div class="otherFilters" style="display: none; padding: 15px 0px 10px 0px">
                            <a href="#" id="dfe_approved" class="btn btn-secondary disabled" style="margin: 3px; pointer-events: auto">DfE approved</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
<div class="modal fade" id="quickStartModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header gray">
                <h4 class="modal-title">Quick start</h4>
                <button type="button" class="close align-middle" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times fa-lg align-middle" style="color: gray"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="quickStartFilters">
                    <div class="quickStartSubjectFilter" style="padding: 15px 0px 10px 0px">
                        <p>
                            Pick some subjects:
                        </p>
                        <p>
                            @foreach ($data['categories'] as $category)
                                <a href="#" id="{{ str_replace(' ', '', $category->category) }}_quickStart" class="btn disabled" style="background-color: {{ $category->colour }}; color: {{ $category->font_colour }}; margin: 3px; pointer-events: auto">{{ $category->category }}</a>
                            @endforeach
                        </p>
                        <br />
                        <div class="d-inline-flex justify-content-between align-items-end" style="width: 90%; vertical-align: bottom;">
                            <button type="button" class="btn btn-secondary" onclick="$('.quickStartSubjectFilter').hide(); $('.quickStartAgeFilter').show()">Next</button>
                            <a href="#" data-dismiss="modal">
                                <small>Skip straight to calendar</small>
                            </a>
                        </div>
                    </div>
                    <div class="quickStartAgeFilter" style="display: none; padding: 15px 0px 10px 0px">
                        <p>
                            Pick one or more age groups:
                        </p>
                        <p>
                            <a href="#" id="littleKids_quickStart" class="btn btn-warning disabled" style="margin: 3px; pointer-events: auto">6 and under</a>
                            <a href="#" id="middleKids_quickStart" class="btn disabled" style="margin: 3px; pointer-events: auto; background-color: orange">7 to 11</a>
                            <a href="#" id="bigKids_quickStart" class="btn btn-info disabled" style="margin: 3px; pointer-events: auto">12 and older</a>
                        </p>
                        <br />
                        <div class="d-inline-flex justify-content-between align-items-end" style="vertical-align: bottom; width: 90%">
                            <button type="button" class="btn btn-secondary mr-auto" onclick="$('.quickStartAgeFilter').hide(); $('.quickStartConfirm').show()">Next</button>
                            <a href="#" data-dismiss="modal">
                                <small>Skip straight to calendar</small>
                            </a>
                        </div>
                    </div>
                    <div class="quickStartConfirm" style="display: none; padding: 15px 0px 10px 0px">
                        <p>You've set some filters! You can change these on the page whenever you like.</p>
                        <p>You can also:
                            <ul>
                                <li>Click on events for information and times</li>
                                <li>Save your favourites by clicking the <i class="far fa-heart fa-lg"></i></li>
                                <li>View a list of great content you can watch any time</li>
                            </ul>
                        </p>
                        <br />
                        <button type="button" class="btn btn-success" data-dismiss="modal">Show my filtered events &raquo;</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header gray">
                <h4 class="modal-title">Please login</h4>
                <button type="button" class="close align-middle" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times fa-lg align-middle" style="color: gray"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <i class="fas fa-exclamation-circle fa-lg align-middle" style="color: red"></i>&nbsp To save your favourites, you will need to <a href="{{ route('register') }}">register</a>. If you have already registered, <a href="{{ route('login') }}">please login</a>.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

<script>

    var isAdmin = @json(Auth::check() ? Auth::user()->isAdmin() : 0);
    var user_id = @json(Auth::check() ? Auth::user()->id : 0);
    var quickStart = @json(isset($data['quickStart']) ? 1 : 0)

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
            minTime: "07:00:00",
            maxTime: "21:00:00",
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
                axios.get('/api/events/calendar')
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
            navLinkDayClick: function(date, jsEvent) {
                calendar.changeView('timeGridDay', date);
            },
            eventRender: function(info) {
                // resize the rows
                $(calendarEl).find('tr[data-time]').css('height', '3em');

                // colour the event based on the category's colour
                $(info.el).css({
                    'background-color': info.event.extendedProps.colour,
                    'border-color': info.event.extendedProps.colour,
                    'color': info.event.extendedProps.font_colour == "black" ? "#212529" : "white",
                    'font-size': '1em',
                }).hover(function(){
                    $(this).css('cursor', 'pointer');
                }).tooltip({
                    title: info.event.extendedProps.description.substring(0, 40) + '... ',
                    placement: "top",
                    trigger: "hover",
                    container: "body"
                });

                // SUBJECT FILTER
                if (!$('#subjectFilter').hasClass('disabled')) {

                    // if subject filter is engaged, find the event's category (remove any spaces, just in case)
                    var category = info.event.extendedProps.category.replace(/ /g, '');
                    // if the filter is active and the subject button is disabled, don't show
                    if ($('.subjectFilter a#' + category).hasClass('disabled')) {
                        return false;
                    }
                }

                // AGE FILTER
                if (!$('#ageFilter').hasClass('disabled')) {
                    // if the age filter is engaged, do the following:
                    if (

                        (!$('#littleKids').hasClass('disabled') && info.event.extendedProps.minimum_age <= 6) || 

                        (!$('#middleKids').hasClass('disabled') && (info.event.extendedProps.minimum_age <= 11 && info.event.extendedProps.maximum_age >= 7)) || 

                        (!$('#bigKids').hasClass('disabled') && info.event.extendedProps.maximum_age >= 12)
                    ) {
                        // these events are to be displayed - done this way for readability
                    } else {
                        return false;
                    }
                }

                // OTHER FILTERS
                if (!$('#otherFilters').hasClass('disabled')) {
                    if (!$('#dfe_approved').hasClass('disabled') && info.event.extendedProps.dfe_approved === 0) {
                        return false;
                    }
                }

                // FAVOURITES FILTER
                if (!$('#showFavourites').hasClass('disabled')) {
                    if (info.event.extendedProps.favourite_id == 0) {
                        return false;
                    }
                }

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
            },
        });

        calendar.render();

        // attach event handlers to MAIN filter buttons, to show which one is active etc
        $(".filter-buttons button").on('click', function(e){
            // find the button that was actually clicked
            var filterButton = $(e.currentTarget);
            // locate the div that relates to the clicked button
            var filterDiv = filterButton.attr('id');
            // ensure the button doesn't stay focused, and replace the caret (up or down)
            filterButton.blur().children().toggleClass('fa-caret-up fa-caret-down');

            // loop around all the filters, and hide those that AREN'T the one clicked
            // to give the impression of one replacing another
            $('.filters div').each(function(e){
                // the class of the div should match the id of the clicked button
                var className = $(this).attr('class');

                if (className !== filterDiv) {
                    // hide the div not related to the clicked button
                    $(this).hide(300);
                } else {
                    // toggle the clicked one
                    $(this).toggle(300);
                }
            });
        });

        // attach event handlers to specific filter buttons ("a") to ensure correct disabling/enabling functionality
        $('.filters a').on('click', function(e){
            e.preventDefault();

            // find the relevant button
            var button = $(e.currentTarget);
            if (button.hasClass('disabled')) {
                // if it's disabled, reenable and remove blur
                button.removeClass('disabled').addClass('btn-lg').blur();
            } else {
                // if it wasn't disabled, disable it but keep the pointer events (so it's still clickable)
                button.addClass('disabled').removeClass('btn-lg').css('pointer-events', 'auto').blur();
            }

            var filterIsOn = false;
            var parentDivClassName = button.parent().attr('class');

            $('.' + parentDivClassName + ' a').each(function(f){
                if ($(this).hasClass('disabled') === false) {
                    filterIsOn = true;
                    return false;
                }
            });

            if (filterIsOn) {
                $('#' + parentDivClassName).attr('class', 'btn btn-success');
            } else {
                $('#' + parentDivClassName).attr('class', 'btn btn-outline-secondary disabled');
            }
            calendar.rerenderEvents();
        });

        $('.quickStartFilters a').on('click', function(e){

            e.preventDefault();
            // find the relevant button
            var button = $(e.currentTarget);
            if (button.hasClass('disabled')) {
                // if it's disabled, reenable and remove blur
                button.removeClass('disabled').addClass('btn-lg').blur();
            } else {
                // if it wasn't disabled, disable it but keep the pointer events (so it's still clickable)
                button.addClass('disabled').removeClass('btn-lg').css('pointer-events', 'auto').blur();
            }
            var id = button.attr('id').replace('_quickStart', '');
            $('#' + id).trigger('click');
        })

        $('#search').on('keyup', function(e){
            if ($(e.currentTarget).val() === '') {
                $("#searchBtn").attr('class', 'btn btn-outline-secondary disabled');
                calendar.rerenderEvents();
            }
            if(e.keyCode === 13) {
                e.preventDefault();
                doSearch();
            }
        });

        // if the search box is used...
        $("#searchBtn").on('click', function(e){
            doSearch();
        });

        var doSearch = function(e) {
            var searchBtn = $("#searchBtn");
            if ($('#search').val() === '') {
                if (searchBtn.hasClass('disabled')) {
                } else {
                    searchBtn.attr('class', 'btn btn-outline-secondary disabled');
                }
            } else {
                if (searchBtn.hasClass('disabled')) {
                    searchBtn.attr('class', 'btn btn-success');
                } else {
                    $('#search').val('');
                    searchBtn.attr('class', 'btn btn-outline-secondary disabled');
                }                
            }

           calendar.rerenderEvents();
        }

        // if the show favourites button is clicked
        $("#showFavourites").on('click', function(e){
            if (user_id) {
                var favouritesBtn = $(e.currentTarget);
                var heart = favouritesBtn.children();
                if (favouritesBtn.hasClass('disabled')) {
                    favouritesBtn.attr('class', 'btn btn-success');
                    heart.toggleClass('fas far').css('color', 'red');
                } else {
                    favouritesBtn.attr('class', 'btn btn-outline-secondary disabled');
                    heart.toggleClass('fas far').css('color', 'gray');
                }
                calendar.rerenderEvents();
            } else {
                $('#loginModal').modal();
            }
        });

        // reset the url so people don't end up pressing back and getting the quick start again
        if (quickStart) {
            window.history.replaceState(null, null, '/calendar');
            $('#quickStartModal').modal();
        }

    });
</script>