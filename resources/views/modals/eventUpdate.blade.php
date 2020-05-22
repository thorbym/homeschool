<div class="modal-dialog" role="document">
    <form action="{{ url('/event/' . $event->id) }}" method="POST">
    {{ csrf_field() }}
        <div class="modal-content">
            <div class="modal-body">
                <h4>Event</h4>
                <br />
                <div class="form-group">
                    <label for="title">Title</label>
                    <input required type="text" class="form-control" name="title" id="title" value="{{ $event->title }}">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" rows="3" name="description" id="description">{{ $event->description }}</textarea>
                </div>

                <div class="form-group">
                    <label for="link">Link</label>
                    <input required type="text" class="form-control" name="link" id="link"  value="{{ $event->link }}">
                </div>

                <div class="form-group">
                    <label for="catchup_link">Watch later</label>
                    <input type="text" class="form-control" name="catchup_link" id="catchup_link"  value="{{ $event->catchup_link }}">
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <div class="col">
                            <label for="start">Start time</label>
                            <input required type="text" class="timepicker form-control" name="start_time" id="start_time" value="{{ $event->start_time }}">
                        </div>
                        <div class="col">
                            <label for="end">End time</label>
                            <input required type="text" class="timepicker form-control" name="end_time" id="end_time" value="{{ $event->end_time }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="days">Days</label>
                    <select required multiple class="form-control" name="days_of_week[]" id="days_of_week">
                        <option value="1" {{ in_array(1, json_decode($event->days_of_week)) ? 'selected' : '' }}>Monday</option>
                        <option value="2" {{ in_array(2, json_decode($event->days_of_week)) ? 'selected' : '' }}>Tuesday</option>
                        <option value="3" {{ in_array(3, json_decode($event->days_of_week)) ? 'selected' : '' }}>Wednesday</option>
                        <option value="4" {{ in_array(4, json_decode($event->days_of_week)) ? 'selected' : '' }}>Thursday</option>
                        <option value="5" {{ in_array(5, json_decode($event->days_of_week)) ? 'selected' : '' }}>Friday</option>
                        <option value="6" {{ in_array(6, json_decode($event->days_of_week)) ? 'selected' : '' }}>Saturday</option>
                        <option value="0" {{ in_array(7, json_decode($event->days_of_week)) ? 'selected' : '' }}>Sunday</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select class="form-control" name="category_id" id="category_id">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $event->category_id == $category->id ? 'selected' : '' }}>{{ $category->category }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="minimum_age">Minimum age</label>
                            <input required type="number" class="form-control" name="minimum_age" id="minimum_age" min=0 max=16 value="{{ $event->minimum_age }}">
                        </div>
                        <div class="col">
                            <label for="maximum_age">Maximum age</label>
                            <input required type="number" class="form-control" name="maximum_age" id="maximum_age" min=0 max=16 value="{{ $event->maximum_age }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="dfe_approved" id="dfe_approved">
                        <label class="form-check-label" for="dfe_approved" {{ $event->dfe_approved ? 'checked' : '' }}>DfE approved</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requires_supervision" id="requires_supervision">
                        <label class="form-check-label" for="requires_supervision" {{ $event->requires_supervision ? 'checked' : '' }}>Supervision required</label>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </div>
    </form>
</div>