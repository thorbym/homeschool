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
                Link: <a href="{{ $event->link }}" target="_blank">{{ $event->link }}</a>
            </p>
            <p>
                Watch again: <a href="{{ $event->catchup_link }}" target="_blank">{{ $event->catchup_link }}</a>
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
            <!--<a href="#" class="tooltip-test" title="Tooltip">This link</a> and <a href="#" class="tooltip-test" title="Tooltip">that link</a> have tooltips on hover.</p>-->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>