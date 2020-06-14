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
                    </div>
                    <div class="filters">
                        <div class="subjectFilter" style="display: none; padding: 15px 0px 10px 0px">
                            @foreach ($categories as $category)
                                <a href="#" id="{{ str_replace(' ', '', strtolower($category->category)) }}" class="btn disabled" style="background-color: {{ $category->colour }}; color: {{ $category->font_colour }}; margin: 3px; pointer-events: auto">{{ $category->category }}</a>
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
                    <div id="list">
                        <table id="nonLiveList" class="table table-hover" style="width: 100%; display: none">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Title</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Age group</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($events as $event)
                                    <tr id="{{ $event->id }}">
                                        <td scope="row">{{ $event->title }}</td>
                                        <td style="font-size: 1.3rem"><span class="badge badge-pill" style="background-color: {{ $event->colour }}; color: {{ $event->font_colour }}">{{ $event->category }}</span></td>
                                        <td data-min="{{ $event->minimum_age }}" data-max="{{ $event->maximum_age }}">{{ $event->minimum_age }} to {{ $event->maximum_age }}</td>
                                        <td>{!! $event->dfe_approved ? '&#x2714' : '' !!}</td>
                                        <td>{!! $event->requires_supervision ? '<i class="fas fa-binoculars"></i>' : '' !!}</td>
                                        @if ($event->favourite_id)
                                            <td class="favouriteTd" data-search="1">
                                                <a class="favourite" href="#">
                                                    <i class="fas fa-heart" id="{{ $event->favourite_id }}" style="color: red"></i>
                                                </a>
                                            </td>
                                        @else
                                            <td class="favouriteTd" data-search="0">
                                                <a class="favourite" href="#">
                                                    <i class="far fa-heart" style="color: gray"></i>
                                                </a>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
    var table = false;
    var user_id = @json(Auth::check() ? Auth::user()->id : 0);

    document.addEventListener('DOMContentLoaded', function() {

        table = $('#nonLiveList').DataTable({
            "columnDefs": [
                { "orderable": false, "targets": [3, 4, 5] },
                { "orderable": true, "targets": [0, 1, 2] },
                { "visible": false, "targets": [2, 3, 4] }
            ],
            "language": {
                searchPlaceholder: "Search title",
                sLengthMenu: "_MENU_",
                search: ""
            },
            "drawCallback": function() {
                loadDatatableFunctions();
            },
            "initComplete": function() {
                $('#nonLiveList').show();
            }
        });

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
        $('.subjectFilter a').on('click', function(e){
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
            var filterString = "";
            var prefix = "";
            $('.' + parentDivClassName + ' a').each(function(){
                if ($(this).hasClass('disabled') === false) {
                    // filter is enabled
                    filterIsOn = true;
                    // build the string that will filter the table
                    filterString += prefix + "^" + $(this).text() + "$";
                    prefix = "|";
                }
            });

            // do the do
            table
            .column(1)
            .search(filterString, true, false)
            .draw();

            if (filterIsOn) {
                $('#' + parentDivClassName).attr('class', 'btn btn-success');
            } else {
                $('#' + parentDivClassName).attr('class', 'btn btn-outline-secondary disabled');
            }

        });

        // attach event handlers to specific filter buttons ("a") to ensure correct disabling/enabling functionality
        $('.ageFilter a').on('click', function(e){
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
            var filterString = "";
            var prefix = "";
            $('.' + parentDivClassName + ' a').each(function(){
                if ($(this).hasClass('disabled') === false) {
                    // filter is enabled
                    filterIsOn = true;
                    if ($(this).attr('id') == "littleKids") {
                        if (filterString != "") {
                            filterString += "|";
                        }
                        filterString += "^0 to |^1 to |^2 to |^3 to |^4 to |^5 to |^6 to ";
                    }
                    if ($(this).attr('id') == "middleKids") {
                        if (filterString != "") {
                            filterString += "|";
                        }
                        filterString += " to 16| to 15| to 14| to 13| to 12| to 11| to 10| to 9| to 8| to 7";
                    }
                    if ($(this).attr('id') == "bigKids") {
                        if (filterString != "") {
                            filterString += "|";
                        }
                        filterString += " to 16| to 15| to 14| to 13| to 12";
                    }
                }
            });

            // do the do
            table
            .column(2)
            .search(filterString, true, false, true)
            .draw();

            if (filterIsOn) {
                $('#' + parentDivClassName).attr('class', 'btn btn-success');
            } else {
                $('#' + parentDivClassName).attr('class', 'btn btn-outline-secondary disabled');
            }

        });

        // attach event handlers to specific filter buttons ("a") to ensure correct disabling/enabling functionality
        $('.otherFilters a').on('click', function(e){
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
            var filterString = "";
            $('.' + parentDivClassName + ' a').each(function(){
                if ($(this).hasClass('disabled') === false) {
                    // filter is enabled
                    filterIsOn = true;
                    // build the string that will filter the table
                    filterString = '✔';
                    return false;
                }
            });

            // do the do
            table
            .column(3)
            .search(filterString)
            .draw();

            if (filterIsOn) {
                $('#' + parentDivClassName).attr('class', 'btn btn-success');
            } else {
                $('#' + parentDivClassName).attr('class', 'btn btn-outline-secondary disabled');
            }

        });

         // if the favourites filter is clicked
        $("#showFavourites").on('click', function(e){
            if (user_id) {
                var favouritesBtn = $(e.currentTarget);
                var heart = favouritesBtn.children();
                if (favouritesBtn.hasClass('disabled')) {
                    favouritesBtn.attr('class', 'btn btn-success');
                    heart.toggleClass('fas far').css('color', 'red');
                    table
                    .column(5)
                    .search('1')
                    .draw();
                } else {
                    favouritesBtn.attr('class', 'btn btn-outline-secondary disabled');
                    heart.toggleClass('fas far').css('color', 'gray');
                    table
                    .column(5)
                    .search('')
                    .draw();
                }
                loadDatatableFunctions();
            } else {
                $('#loginModal').modal();
            }
        });

        loadDatatableFunctions();
        
        $('ul.pagination li').on('click', function(){
            loadDatatableFunctions();
        });

        $('.custom-select').on('change', function(){
            loadDatatableFunctions();
        });

    });
    
    var loadDatatableFunctions = function() {

        $('tbody tr th,td:not(.favouriteTd)').off('click').on('click', function(e){
            var id = $(e.currentTarget).closest('tr').attr('id');
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
        });

        $('.favourite').off('click').on('click', function(e){
            if (user_id) {
                var heart = $(e.currentTarget).children();
                if (heart.hasClass('fas')) {
                    var id = heart.attr('id');
                    axios.delete('/api/favourite/' + id)
                        .then(function (response) {
                            heart.toggleClass('fas far').css('color', 'gray');
                            heart.closest('td').attr('data-search', '0');
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                } else {
                    event_id = $(e.currentTarget).closest('tr').attr('id');
                    axios.post('/api/favourite', {
                            event_id: event_id,
                            user_id: user_id
                        })
                        .then(function (response) {
                            heart.toggleClass('fas far').css('color', 'red');
                            heart.closest('td').attr('data-search', '1');
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                }
            } else {
                $('#loginModal').modal();
            }
            return false;
        });
    }
</script>