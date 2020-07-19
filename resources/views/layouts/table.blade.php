<table id="nonLiveList" class="table table-hover" style="width: 100%; display: none">
    <thead class="thead-dark">
        <tr>
            <th scope="col">Title</th>
            <th scope="col">Subject</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($events as $event)
            <tr id="{{ $event->id }}">
                <td scope="row">{{ $event->title }}</td>
                <td style="font-size: 1.3rem"><span class="badge badge-pill" style="background-color: {{ $event->colour }}; color: {{ $event->font_colour }}">{{ $event->category }}</span></td>
                @if ($event->favourite_id)
                    <td class="favouriteTd">
                        <a class="favourite" href="#">
                            <i class="fas fa-heart" id="{{ $event->favourite_id }}" style="color: red"></i>
                        </a>
                    </td>
                @else
                    <td class="favouriteTd">
                        <a class="favourite" href="#">
                            <i class="far fa-heart" style="color: gray"></i>
                        </a>
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    var isAdmin = @json(Auth::check() ? Auth::user()->isAdmin() : 0);
    var user_id = @json(Auth::check() ? Auth::user()->id : 0);

    $(document).ready(function() {

        $('#nonLiveList').DataTable({
            "columnDefs": [
                { "orderable": true, "targets": [0, 1] },
                { "orderable": false, "targets": [2] }
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

    });
    
    var loadDatatableFunctions = function() {

        $('tbody tr th,td:not(.favouriteTd)').off('click').on('click', function(e){
            var eventId = $(e.currentTarget).closest('tr').attr('id');
            if (isAdmin) {
                // admin can edit the events, so get event edit form
                axios.get('/api/event/' + eventId + '/editFromList')
                    .then(function (response) {
                        $('#eventModal').html(response.data).modal();
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            } else {
                // otherwise it is normal user, so show the "details" modal
                axios.get('/api/event/' + eventId + '/showFromList')
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
                            if (!$('#showFavourites').hasClass('disabled')) {
                                $(e.currentTarget).closest('tr').remove();
                            }
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
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                }
            } else {
                axios.get('/api/loginWarning/show')
                    .then(function (response) {
                        $('#loginModal').html(response.data).modal();
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            }
            return false;
        });
    }
</script>