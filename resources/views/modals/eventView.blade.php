<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header gray">
            <h4 class="modal-title">{{ $event->title }}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <h5>Description</h5>
            <p>{{ $event->description }}</p>
            <br />
            <hr>
            <h5>Details</h5>
            <p>
                <div class="row">
                    <div class="col-md-6">
                        Time: {{ $event->start_time }} to {{ $event->end_time }}
                    </div>
                    <div class="col-md-6">
                        Suitable for ages: {{ $event->minimum_age }} to {{ $event->maximum_age }}
                    </div>
                </div>
            </p>
            <p>
                Days: {{ Helper::convertDaysOfWeek($event->days_of_week) }}
            </p>
            <p>
                <div class="row">
                    <div class="col-md-6">
                        DfE Approved: &nbsp
                        @if ($event->dfe_approved)
                            <i class="fas fa-check fa-lg" style="color: green"></i>
                        @else
                            <i class="fas fa-times fa-lg" style="color: gray"></i>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if ($event->requires_supervision)
                            <i class="fas fa-binoculars fa-lg" style="color: orange"></i> &nbsp
                            Adult supervision required!
                        @endif
                    </div>
                </div>
            </p>
            <br />
            <hr>
            <h5>Viewing info</h5>
            <br />
            <p>
                <a href="{{ $event->link }}" target="_blank">Click here for the LIVE event</a>
            </p>
            <p>
                <a href="{{ $event->catchup_link }}" target="_blank">Click here to watch anytime</a>
            </p>
            <!--<a href="#" class="tooltip-test" title="Tooltip">This link</a> and <a href="#" class="tooltip-test" title="Tooltip">that link</a> have tooltips on hover.</p>-->
        </div>
        <div class="modal-footer">
            <a id="favourite" href="#">
                @if ($event->favourite_id)
                    <i class="fas fa-heart fa-2x" id="{{ $event->favourite_id }}" style="color: red"></i>
                @else
                    <i class="far fa-heart fa-2x" style="color: gray"></i>
                @endif
            </a>
            &nbsp&nbsp
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<script>

    var user_id = @json(Auth::check() ? Auth::user()->id : 0);
    var event_id = @json($event->id);

    $(document).ready(function(){
        if (user_id) {
            $('#favourite').on('click', function(e){
                var heart = $(e.currentTarget).children();
                if (heart.hasClass('fas')) {
                    var id = heart.attr('id');
                    axios.delete('/api/favourite/' + id)
                        .then(function (response) {
                            heart.toggleClass('fas far').css('color', 'gray');
                            if ($('table tr#' + event_id + ' td.favouriteTd')) {
                                var tableIcon = $('table tr#' + event_id + ' td.favouriteTd');
                                tableIcon.attr('data-search', '0').children().children().toggleClass('fas far').css('color', 'gray').remove('id');
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                } else {
                    axios.post('/api/favourite', {
                            event_id: event_id,
                            user_id: user_id
                        })
                        .then(function (response) {
                            heart.toggleClass('fas far').css('color', 'red');
                            if ($('table tr#' + event_id + ' td.favouriteTd')) {
                                var tableIcon = $('table tr#' + event_id + ' td.favouriteTd');
                                tableIcon.attr('data-search', '1').children().children().toggleClass('fas far').css('color', 'red').attr('id', response.data.id);
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                    });
                }
            });
        }
    })
</script>