<table id="nonLiveList" class="table table-hover" style="width: 100%; display: none">
    <thead class="thead-light">
        <tr style="display: none">
            <th scope="col">Image</th>
            <th scope="col">Title</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($events as $event)
            <tr data-url="{{ url('/event/' . $event->id . '/showFromList') }}">
                <td>
                    @if ($event->image_link)
                        <img width="100" style="border-radius: 10px" src="{{ $event->image_link }}" alt="no image">
                    @elseif ($event->video_link)
                        <img width="100" style="border-radius: 10px" src="https://img.youtube.com/vi/{{ $event->video_link }}/hqdefault.jpg" alt="no image">
                    @elseif ($event->image_file_id)
                        <img width="100" style="border-radius: 10px" src="{{ url('storage/'.$event->image_file_id) }}" alt="no image">
                    @else
                        @if (strpos($event->description, '//www.youtube.com/embed/') !== false)    
                            @php $firstPos = strpos($event->description, '//www.youtube.com/embed/') + 24 @endphp
                            @php $lastPos = strpos($event->description, '"', $firstPos) @endphp
                            @php $youtubeLink = substr($event->description, $firstPos, ($lastPos - $firstPos)) @endphp
                            <img width="100" style="border-radius: 10px" src="https://img.youtube.com/vi/{{ $youtubeLink }}/hqdefault.jpg" alt="no image">
                        @elseif (strpos($event->description, 'img src=') !== false)    
                            @php $firstPos = strpos($event->description, 'img src=') + 9 @endphp
                            @php $lastPos = strpos($event->description, '"', $firstPos) @endphp
                            @php $pictureLink = substr($event->description, $firstPos, ($lastPos - $firstPos)) @endphp
                            <img width="100" style="border-radius: 10px" src="{{ $pictureLink }}" alt="no image">
                        @else
                            <img width="100" style="border-radius: 10px" src="{{ asset('img/no_image_placeholder.jpg') }}" alt="no image">
                        @endif                        
                    @endif
                </td>
                <td scope="row">
                    {{ $event->title }}<br />
                    @if ($event->average_rating > 0)
                        {!! Helper::getRatingStars($event->average_rating, 'fa-xs') !!}
                    @endif
                </td>
                <td class="favouriteTd">
                    <div class="row">
                        @if ($event->favourite_id)
                            <i class="fas fa-heart" id="{{ $event->favourite_id }}" style="color: red"></i>
                        @endif
                        @if (Auth::check() && Auth::user()->isAdmin())
                            <a href="{{ url('/event/' . $event->id . '/edit') }}" class="btn btn-link">Edit</a>
                        @endif
                    </div>
                </td>
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
                { "orderable": true, "targets": [0] },
                { "orderable": false, "targets": [1, 2] }
            ],
            "thead": [
                {'display': 'none'}
            ],
            "language": {
                searchPlaceholder: "Search events",
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

        $('tbody tr').on('click', function(e){
            var url = $(e.currentTarget).closest('tr').data('url');
            window.location = url;
        });
    }
</script>