<div class="modal-dialog" role="document">
    @if ($event)
        <form action="{{ url('/event/' . $event->id) }}" method="POST">
        @method('PATCH')
    @else
        <form action="{{ url('/event') }}" method="POST">
    @endif
    @csrf
        <div class="modal-content">
            <div class="modal-body">
                <h4>{{ $event ? $event->title : 'New event' }}</h4>
                <br />
                <div class="form-group">
                    <label for="title">Title</label>
                    <input required type="text" class="form-control" name="title" id="title" value="{{ $event ? $event->title : '' }}">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea required class="form-control" rows="3" name="description" id="description">{{ $event ? $event->description : '' }}</textarea>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="liveEvent" {{ $event && $event->start_time ? 'checked' : '' }}>
                        <label class="form-check-label" for="requires_supervision">Live event?</label>
                    </div>
                </div>
                
                <div id="liveEventInfo" style="display: {{ $event && $event->start_time ? 'auto' : 'none' }}">
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col">
                                <label for="start">Start time</label>
                                <input type="text" class="timepicker form-control" name="start_time" id="start_time" value="{{ $event && $event->start_time ? $event->start_time : '' }}">
                            </div>
                            <div class="col">
                                <label for="end">End time</label>
                                <input type="text" class="timepicker form-control" name="end_time" id="end_time" value="{{ $event && $event->end_time ? $event->end_time : '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="days">Days</label>
                        <select multiple class="form-control" name="days_of_week[]" id="days_of_week">
                            <option value="1" {{ $event && $event->days_of_week && in_array(1, json_decode($event->days_of_week)) ? 'selected' : '' }}>Monday</option>
                            <option value="2" {{ $event && $event->days_of_week && in_array(2, json_decode($event->days_of_week)) ? 'selected' : '' }}>Tuesday</option>
                            <option value="3" {{ $event && $event->days_of_week && in_array(3, json_decode($event->days_of_week)) ? 'selected' : '' }}>Wednesday</option>
                            <option value="4" {{ $event && $event->days_of_week && in_array(4, json_decode($event->days_of_week)) ? 'selected' : '' }}>Thursday</option>
                            <option value="5" {{ $event && $event->days_of_week && in_array(5, json_decode($event->days_of_week)) ? 'selected' : '' }}>Friday</option>
                            <option value="6" {{ $event && $event->days_of_week && in_array(6, json_decode($event->days_of_week)) ? 'selected' : '' }}>Saturday</option>
                            <option value="0" {{ $event && $event->days_of_week && in_array(7, json_decode($event->days_of_week)) ? 'selected' : '' }}>Sunday</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="live_youtube_link">LIVE YouTube Link</label>
                        <input  type="text" class="form-control" name="live_youtube_link" id="live_youtube_link"  value="{{ $event ? $event->live_youtube_link : '' }}">
                    </div>

                    <div class="form-group">
                        <label for="live_facebook_link">LIVE Facebook Link</label>
                        <input type="text" class="form-control" name="live_facebook_link" id="live_facebook_link"  value="{{ $event ? $event->live_facebook_link : '' }}">
                    </div>

                    <div class="form-group">
                        <label for="live_instagram_link">LIVE Instagram Link</label>
                        <input type="text" class="form-control" name="live_instagram_link" id="live_instagram_link"  value="{{ $event ? $event->live_instagram_link : '' }}">
                    </div>

                    <div class="form-group">
                        <label for="live_web_link">LIVE Web Link</label>
                        <input type="text" class="form-control" name="live_web_link" id="live_web_link"  value="{{ $event ? $event->live_web_link : '' }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="youtube_link">YouTube Link</label>
                    <input type="text" class="form-control" name="youtube_link" id="youtube_link"  value="{{ $event ? $event->youtube_link : '' }}">
                </div>

                <div class="form-group">
                    <label for="facebook_link">Facebook Link</label>
                    <input type="text" class="form-control" name="facebook_link" id="facebook_link"  value="{{ $event ? $event->facebook_link : '' }}">
                </div>

                <div class="form-group">
                    <label for="instagram_link">Instagram Link</label>
                    <input type="text" class="form-control" name="instagram_link" id="instagram_link"  value="{{ $event ? $event->instagram_link : '' }}">
                </div>

                <div class="form-group">
                    <label for="web_link">Web Link</label>
                    <input type="text" class="form-control" name="web_link" id="web_link"  value="{{ $event ? $event->web_link : '' }}">
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select required class="form-control" name="category_id" id="category_id">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $event && $event->category_id == $category->id ? 'selected' : '' }}>{{ $category->category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="minimum_age">Minimum age</label>
                            <input required type="number" class="form-control" name="minimum_age" id="minimum_age" min=0 max=16 value="{{ $event ? $event->minimum_age : '' }}">
                        </div>
                        <div class="col">
                            <label for="maximum_age">Maximum age</label>
                            <input required type="number" class="form-control" name="maximum_age" id="maximum_age" min=0 max=16 value="{{ $event ? $event->maximum_age : '' }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="dfe_approved" id="dfe_approved" {{ $event && $event->dfe_approved ? 'checked' : '' }}>
                        <label class="form-check-label" for="dfe_approved">DfE approved</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requires_supervision" id="requires_supervision" {{ $event && $event->requires_supervision ? 'checked' : '' }}>
                        <label class="form-check-label" for="requires_supervision">Supervision required</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="free_content" id="free_content" {{ !$event || $event->free_content ? 'checked' : '' }}>
                        <label class="form-check-label" for="free_content">Free content</label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
                @if (Auth::check() && Auth::user()->isAdmin())
                    @if ($event)
                        <button class="btn btn-danger" onclick="changeMethod()">Delete</button>
                    @endif
                    <button type="submit" class="btn btn-primary">Save</button>
                @endif
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">

    $(document).ready(function() {

        // timepicker for modal
        $('.timepicker').timepicker({
            timeFormat: 'HH:mm',
            minTime: '07:00',
            maxTime: '18:00',
            interval: 30,
            zindex: 9999999
        });

        $('#liveEvent').on('click', function(e){
            $('#liveEventInfo').toggle();
            if ($(e.currentTarget).not(':checked')) {
                $('[id^=live_]').val('');
                $('#days_of_week option').prop('selected', false);
                $('#start_time').val('');
                $('#end_time').val('');
            }
        })


    });

    var changeMethod = function () {
        if (confirm('Are you SURE you want to delete?')) {
            $('form input[name="_method"]').val('DELETE').submit();
        }
    }

</script>