@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
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
                        <div class="input-group mb-3" style="margin-bottom: 0px !important">
                            <input type="text" id="search" class="form-control">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary disabled" id="searchBtn" type="button"><i class="fas fa-search"></i></button>
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
                            <!--<a href="#" id="requires_supervision" class="btn btn-secondary disabled" style="margin: 3px; pointer-events: auto">Require supervision</a>-->
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
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
@endsection

<script>

    var isAdmin = @json(Auth::check() ? Auth::user()->isAdmin() : 0);
    var events = @json($data['events']);

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

        var calendar = new Calendar.Calendar(calendarEl, {
            defaultView: 'timeGridDay',
            minTime: "07:00:00",
            maxTime: "21:00:00",
            header: {
                left: mobileCheck() ? 'prev' : 'prev,next',
                center: 'title',
                right: mobileCheck() ? 'next' : 'timeGridDay,timeGridWeek'
            },
            plugins: [ bootstrapPlugin, timeGridPlugin, isAdmin ? interaction : '' ],
            themeSystem: 'bootstrap',
            events: events,
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
                $(calendarEl).find('tr[data-time]').css('height', '2.5em');

                // colour the event based on the category's colour
                $(info.el).css({
                    'background-color': info.event.extendedProps.colour,
                    'border-color': info.event.extendedProps.colour,
                    'color': info.event.extendedProps.font_colour == "black" ? "#212529" : "white"
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
                    axios.get('/api/event/0')
                        .then(function (response) {
                            $('#editModal').html(response.data).modal();
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                }
            },
            eventClick: function(info) {
                var id = info.event.id;
                axios.get('/api/event/' + id)
                    .then(function (response) {
                        $('#viewModal').html(response.data).modal();
                    })
                    .catch(function (error) {
                        console.log(error);
                });
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

        $('#search').on('keyup', function(e){
            if ($(e.currentTarget).val() === '') {
                $("#searchBtn").attr('class', 'btn btn-outline-secondary disabled');
            }
        });

        // if the search box is used...
        $("#searchBtn").on('click', function(e){
            var searchBtn = $(e.currentTarget);
            if ($('#search').val() === '') {
                if (searchBtn.hasClass('disabled')) {
                    return false;
                } else {
                    searchBtn.attr('class', 'btn btn-outline-secondary disabled');
                    return false
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
        });

        // if the favourites button is
        $("#showFavourites").on('click', function(e){
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
        });

    });
</script>