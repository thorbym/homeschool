@extends('layouts.app')

@section('content')

@include('layouts.back')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if ($event)
                <form action="{{ url('/event/' . $event->id) }}" method="POST" enctype="multipart/form-data">
                @method('PATCH')
            @else
                <form action="{{ url('/event') }}" method="POST" enctype="multipart/form-data">
            @endif
            @csrf
                <h3>{{ $event ? $event->title : 'Add an event' }}</h3>
                <br />
                <div class="modal-content">
                    <div class="modal-body">
                        <h5>General Info</h5><br />
                        <div class="form-group row">
                            <label for="title" class="col-sm-2 col-form-label">Title</label>
                            <div class="col-sm-10">
                                <input required type="text" class="form-control" name="title" id="title" value="{{ old('title', $event->title ?? '' ) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-sm-2 col-form-label">Description</label>
                            <div class="col-sm-10">
                                <textarea required class="form-control" rows="3" name="description" id="description">{{ old('description', $event->description ?? '') }}</textarea>
                            </div>
                        </div>
                        
                        @if ($event && $event->image_file_id)
                            <div class="form-group row">
                                <label for="image" class="col-sm-2 col-form-label">Image</label>
                                <img width="100px" style="border-radius: 10px" src="{{ url('storage/'.$event->image_file_id) }}" alt="no image">
                            </div>
                        @else
                            <div class="form-group row">
                                <label for="image" class="col-sm-2 col-form-label">Add an image</label>
                                <input type="file" id="image" name="image" accept="image/png, image/jpeg">
                            </div>
                        @endif

                        <div class="form-group row">
                            <label for="image_link" class="col-sm-2 col-form-label">Image link<br /><small>(whole link)</small></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="image_link" id="image_link" value="{{ old('image_link', $event->image_link ?? '' ) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="video_link" class="col-sm-2 col-form-label">Video link<br /><small>(ONLY unique code)</small></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="video_link" id="video_link" value="{{ old('video_link', $event->video_link ?? '' ) }}">
                            </div>
                        </div>

                        <br /><hr><br />
                        
                        <div class="form-group row">
                            <label class="col-sm-2 form-check-label">Live event?</label>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="liveEvent" {{ old('start_time', $event->start_time ?? '') ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                        
                        <div id="liveEventInfo" style="display: {{ old('start_time', $event && $event->start_time) ? 'auto' : 'none' }}">
                            <h5>Live Event Information</h5><br />
                            <div class="form-group row">
                                <label for="start" class="col-sm-2 col-form-label">Start time</label>
                                <div class="col-sm-4">
                                    <input type="text" class="timepicker form-control" name="start_time" id="start_time" value="{{ old('start_time', $event->start_time ?? '') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="end" class="col-sm-2 col-form-label">End time</label>
                                <div class="col-sm-4">
                                    <input type="text" class="timepicker form-control" name="end_time" id="end_time" value="{{ old('end_time', $event->end_time ?? '') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="days" class="col-sm-2 col-form-label">Days</label>
                                <div class="col-sm-4">
                                    <select multiple class="form-control" name="days_of_week[]" id="days_of_week">
                                        <option value="1" {{ @in_array(1, old('days_of_week', json_decode($event->days_of_week))) ? 'selected' : '' }}>Monday</option>
                                        <option value="2" {{ @in_array(2, old('days_of_week', json_decode($event->days_of_week))) ? 'selected' : '' }}>Tuesday</option>
                                        <option value="3" {{ @in_array(3, old('days_of_week', json_decode($event->days_of_week))) ? 'selected' : '' }}>Wednesday</option>
                                        <option value="4" {{ @in_array(4, old('days_of_week', json_decode($event->days_of_week))) ? 'selected' : '' }}>Thursday</option>
                                        <option value="5" {{ @in_array(5, old('days_of_week', json_decode($event->days_of_week))) ? 'selected' : '' }}>Friday</option>
                                        <option value="6" {{ @in_array(6, old('days_of_week', json_decode($event->days_of_week))) ? 'selected' : '' }}>Saturday</option>
                                        <option value="0" {{ @in_array(7, old('days_of_week', json_decode($event->days_of_week))) ? 'selected' : '' }}>Sunday</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="timezone" class="col-sm-2 col-form-label">Timezone location</label>
                                <div class="col-sm-4">
                                    <select required class="form-control" name="timezone" id="timezone">
                                        @foreach ($timezones as $optgroup => $info)
                                            <optgroup label="{{ $optgroup }}">
                                            @foreach ($info as $key => $val)
                                                <option value="{{ $key }}" {{ old('timezone', $event->timezone ?? '') == $key ? 'checked' : '' }}>{{ $val }}</option>
                                            @endforeach
                                            </optgroup>
                                        @endforeach                                        
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="live_youtube_link" class="col-sm-2 col-form-label">LIVE YouTube Link</label>
                                <div class="col-sm-10">
                                    <input  type="text" class="form-control" name="live_youtube_link" id="live_youtube_link"  value="{{ old('live_youtube_link', $event->live_youtube_link ?? '') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="live_facebook_link" class="col-sm-2 col-form-label">LIVE Facebook Link</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="live_facebook_link" id="live_facebook_link"  value="{{ old('live_facebook_link', $event->live_facebook_link ?? '') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="live_instagram_link" class="col-sm-2 col-form-label">LIVE Instagram Link</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="live_instagram_link" id="live_instagram_link"  value="{{ old('live_instagram_link', $event->live_instagram_link ?? '') }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="live_web_link" class="col-sm-2 col-form-label">LIVE Web Link</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="live_web_link" id="live_web_link"  value="{{ old('live_web_link', $event->live_web_link ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <br /><hr><br />
                        <h5>Event Information</h5><br />
                        <div class="form-group row">
                            <label for="youtube_link" class="col-sm-2 col-form-label">YouTube Link</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control @error('youtube_link') is-invalid @enderror" name="youtube_link" id="youtube_link"  value="{{ old('youtube_link', $event->youtube_link ?? '') }}">
                                @if ($errors->has('youtube_link'))
                                    <small class="text-danger">{{ $errors->first('youtube_link') }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="facebook_link" class="col-sm-2 col-form-label">Facebook Link</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="facebook_link" id="facebook_link"  value="{{ old('facebook_link', $event->facebook_link ?? '') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="instagram_link" class="col-sm-2 col-form-label">Instagram Link</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="instagram_link" id="instagram_link"  value="{{ old('instagram_link', $event->instagram_link ?? '') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="web_link" class="col-sm-2 col-form-label">Web Link</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="web_link" id="web_link"  value="{{ old('web_link', $event->web_link ?? '') }}">
                            </div>
                        </div>

                        <br /><hr><br />
                        <h5>Other Details</h5><br />
                        <div class="form-group row">
                            <label for="category_id" class="col-sm-2 col-form-label">Category</label>
                            <div class="col-sm-4">
                                <select required class="form-control" name="category_id" id="category_id">
                                    <option value=""></option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $event->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->category }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="minimum_age" class="col-sm-2 col-form-label">Minimum age</label>
                            <div class="col-sm-4">
                                <input required type="number" class="form-control" name="minimum_age" id="minimum_age" min=0 max=16 value="{{ old('minimum_age', $event->minimum_age ?? '') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="maximum_age" class="col-sm-2 col-form-label">Maximum age</label>
                            <div class="col-sm-4">
                                <input required type="number" class="form-control" name="maximum_age" id="maximum_age" min=0 max=16 value="{{ old('maximum_age', $event->maximum_age ?? '') }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="requires_supervision" class="col-sm-2 form-check-label">Supervision Required</label>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requires_supervision" id="requires_supervision" {{ old('requires_supervision', $event->requires_supervision ?? '') ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="free_content" class="col-sm-2 form-check-label">Free Content</label>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="free_content" id="free_content" {{ old('free_content') === "off" || $event && !$event->free_content ? '' : 'checked' }}>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal" onclick="window.history.back()">Back</button>
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
    </div>
</div>
@endsection
<script type="text/javascript" defer>

    var timezone = @json($event && $event->timezone ? $event->timezone : 0);

    document.addEventListener('DOMContentLoaded', function() {

        $('#liveEvent').on('click', function(e){
            $('#liveEventInfo').toggle(300);
            if ($(e.currentTarget).not(':checked')) {
                $('[id^=live_]').val('');
                $('#days_of_week option').prop('selected', false);
                $('#start_time').val('');
                $('#end_time').val('');
            }
        })

        if (!timezone) {
            $('#timezone').val(Intl.DateTimeFormat().resolvedOptions().timeZone);
        } else {
            $('#timezone').val(timezone);
        }
        
        $('textarea').summernote({
            height: 200,
        });

        $('.timepicker').timepicker({
            timeFormat: 'HH:mm',
            minTime: '07:00',
            maxTime: '21:00',
            interval: 30,
            zindex: 9999999
        });

    });

    var changeMethod = function () {
        if (confirm('Are you SURE you want to delete?')) {
            $('form input[name="_method"]').val('DELETE').submit();
        }
    }

</script>