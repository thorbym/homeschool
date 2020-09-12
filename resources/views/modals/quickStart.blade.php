<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header gray">
            <h4 class="modal-title">Quick start</h4>
            <button type="button" class="close align-middle" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fas fa-times fa-lg align-middle" style="color: gray"></i></span>
            </button>
        </div>
        <div class="modal-body">
            <div class="quickStartFilters">
                <div class="quickStartSubjectFilter" style="padding: 15px 0px 10px 0px">
                    <p>
                        Pick some subjects:
                    </p>
                    <p>
                        @foreach ($categories as $category)
                            <a href="#" id="{{ $category->id }}_quickStart" class="btn disabled" style="background-color: {{ $category->colour }}; color: {{ $category->font_colour }}; margin: 3px; pointer-events: auto">{{ $category->category }}</a>
                        @endforeach
                    </p>
                    <br />
                    <div class="d-inline-flex justify-content-between align-items-end" style="width: 90%; vertical-align: bottom;">
                        <button type="button" class="btn btn-secondary" onclick="$('.quickStartSubjectFilter').hide(); $('.quickStartAgeFilter').show()">Next</button>
                        <a href="#" data-dismiss="modal">
                            <small>Skip straight to list</small>
                        </a>
                    </div>
                </div>
                <div class="quickStartAgeFilter" style="display: none; padding: 15px 0px 10px 0px">
                    <p>
                        Pick one or more age groups:
                    </p>
                    <p>
                        <a href="#" id="littleKids_quickStart" class="btn btn-warning disabled" style="margin: 3px; pointer-events: auto">6 and under</a>
                        <a href="#" id="middleKids_quickStart" class="btn disabled" style="margin: 3px; pointer-events: auto; background-color: orange">7 to 11</a>
                        <a href="#" id="bigKids_quickStart" class="btn btn-info disabled" style="margin: 3px; pointer-events: auto">12 and older</a>
                    </p>
                    <br />
                    <div class="d-inline-flex justify-content-between align-items-end" style="vertical-align: bottom; width: 90%">
                        <button type="button" class="btn btn-secondary mr-auto" onclick="$('.quickStartAgeFilter').hide(); $('.quickStartConfirm').show()">Next</button>
                        <a href="#" data-dismiss="modal">
                            <small>Skip straight to list</small>
                        </a>
                    </div>
                </div>
                <div class="quickStartConfirm" style="display: none; padding: 15px 0px 10px 0px">
                    <p>You've set some filters! You can change these on the page whenever you like.</p>
                    <p>You can also:
                        <ul>
                            <li>Click on events for information and times</li>
                            <li>Save your favourites by clicking the <i class="far fa-heart fa-lg"></i></li>
                            <li>View a list of great content you can watch any time</li>
                        </ul>
                    </p>
                    <br />
                    <button type="button" class="btn btn-success" data-dismiss="modal">Show my filtered events &raquo;</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function() {

        $('.quickStartFilters a').on('click', function(e){
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
            var id = button.attr('id').replace('_quickStart', '');
            $('#' + id).trigger('click');
        })

    });
</script>