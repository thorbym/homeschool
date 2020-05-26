@extends('layouts.app')

@section('content')

@php
    $events = $data['events']
@endphp

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
                        <!--<div class="input-group mb-3" style="margin-bottom: 0px !important">
                            <input type="text" id="search" placeholder="Search title" class="form-control">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary disabled" id="searchBtn" type="button"><i class="fas fa-search"></i></button>
                            </div>
                        </div>-->
                    </div>
                    <div class="filters">
                        <div class="subjectFilter" style="display: none; padding: 15px 0px 10px 0px">
                            @foreach ($data['categories'] as $category)
                                <a href="#" id="{{ str_replace(' ', '', $category->category) }}" class="btn disabled" style="background-color: {{ $category->colour }}; margin: 3px; pointer-events: auto">{{ $category->category }}</a>
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
                    <div id="list">
                        <table id="nonLiveList" class="table table-hover" style="line-height: 3rem; font-size: 0.9rem">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Title</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Age group</th>
                                    <th scope="col">DfE approved</th>
                                    <th scope="col">Supervision required</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($events as $event)
                                    <tr id="{{ $event->id }}">
                                        <th scope="row">{{ $event->title }}</th>
                                        <td>{{ $event->description }}</td>
                                        <td><span class="badge badge-pill" style="background-color: {{ $event->colour }}">{{ $event->category }}</span></td>
                                        <td>{{ $event->minimum_age }} to {{ $event->maximum_age }}</td>
                                        <td>{!! $event->dfe_approved ? '<i class="fas fa-check"></i>' : '' !!}</td>
                                        <td>{!! $event->requires_supervision ? '<i class="fas fa-binoculars"></i>' : '' !!}</td>
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
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
@endsection

<script>

    var isAdmin = @json(Auth::user()->isAdmin());
    var events = @json($data['events']);

    document.addEventListener('DOMContentLoaded', function() {

        $('#nonLiveList').DataTable();

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

        $('tbody tr').on('click', function(e){
            var id = $(e.currentTarget).attr('id');
            console.log(id);
            axios.get('/api/event/' + id)
                .then(function (response) {
                    $('#viewModal').html(response.data).modal();
                })
                .catch(function (error) {
                    console.log(error);
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
/*
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
*/
    });
</script>