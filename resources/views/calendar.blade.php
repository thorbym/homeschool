@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    Click to filter:&nbsp
                    @foreach ($data['categories'] as $category)
                        <a href="#" id="{{ str_replace(' ', '', $category->category) }}" class="btn btn-sm" style="background-color: {{ $category->colour }}; margin: 3px">{{ $category->category }}</a>
                    @endforeach
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
@endsection

<script>

    var isAdmin = @json(Auth::user()->isAdmin());
    var events = @json($data['events']);

    document.addEventListener('DOMContentLoaded', function() {

        // timepicker for modal
        $('.timepicker').timepicker({
            timeFormat: 'HH:mm',
            minTime: '07:00',
            maxTime: '18:00',
            interval: 30,
            zindex: 9999999
        });

        // fullcalendar stuff
        var calendarEl = document.getElementById('calendar');

        var calendar = new Calendar.Calendar(calendarEl, {
            defaultView: 'timeGridDay',
            minTime: "07:00:00",
            maxTime: "20:00:00",
            header: {
                left: 'prev,next',
                center: 'title',
                right: 'timeGridDay,timeGridWeek'
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
                $(calendarEl).find('tr[data-time]').css('height', '2.5em');
                // find the event's category
                var category = info.event.extendedProps.category;
                // remove the spaces, so it matches with the id of the corresponding button
                category = category.replace(/ /g, '');
                var colour = info.event.extendedProps.colour;
                //info.event.setProp('eventBackgroundColor', colour);
                $(info.el).css({'background-color': colour, 'border-color': colour});
                // check if the matching filter button is disabled - if yes, don't show the event
                var filterButton = $('a#' + category);
                return !(filterButton.hasClass('disabled'))
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
                        $('#editModal').html(response.data).modal();
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            },
        });

        calendar.render();

        $("a[class^='btn']").on('click', function(e){
            e.preventDefault();
            var button = $(e.currentTarget);
            if (button.hasClass('disabled')) {
                button.removeClass('disabled').blur();
            } else {
                button.addClass('disabled').css('pointer-events', 'auto').blur();
            }
            calendar.rerenderEvents();
        });

    });
</script>