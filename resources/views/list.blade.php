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
                    <div id="list">
                        <!-- table goes in here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
@endsection

<script>

    document.addEventListener('DOMContentLoaded', function() {

        window.drawTable = function() {
            var filtersArr = {};
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
            axios.get('/api/events/list/' + JSON.stringify(filtersArr))
                .then(function (response) {
                    $('#list').html(response.data)
                })
                .catch(function (error) {
                    console.log(error);
                    failureCallback(err)
                });
        }
        drawTable();
    });

</script>