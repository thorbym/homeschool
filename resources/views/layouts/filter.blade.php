<div class="filter-buttons form-inline">
    <button type="button" class="btn btn-outline-secondary disabled" id="subjectFilter">
        Subject 
        <i class="fas fa-caret-down"></i>
    </button>&nbsp&nbsp
    <button type="button" class="btn btn-outline-secondary disabled" id="ageFilter">
        Age 
        <i class="fas fa-caret-down"></i>
    </button>&nbsp&nbsp
    <button type="button" class="btn btn-outline-secondary disabled" id="ratingsFilter">
        Ratings 
        <i class="fas fa-caret-down"></i>
    </button>&nbsp&nbsp
    <button type="button" class="btn btn-outline-secondary disabled" id="otherFilters">
        Other 
        <i class="fas fa-caret-down"></i>
    </button>&nbsp&nbsp
    <button type="button" class="btn btn-outline-secondary disabled" id="showFavourites">
        &nbsp<i class="far fa-heart"></i>&nbsp
    </button>&nbsp&nbsp
    @if (last(request()->segments()) == 'calendar')
        <div class="d-none d-sm-block">
            <div class="input-group mb-3" style="margin-bottom: 0px !important">
                <input type="text" id="search" class="form-control">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary disabled" id="searchBtn" type="button"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    @endif
</div>
<div class="filters">
    <div class="subjectFilter" style="display: none; padding: 15px 0px 10px 0px">
        @foreach ($data['categories'] as $category)
            <a href="#" id="subjectFilter_{{ $category->id }}" class="btn disabled" style="background-color: {{ $category->colour }}; color: {{ $category->font_colour }}; margin: 3px; pointer-events: auto">{{ $category->category }}</a>
        @endforeach
    </div>
    <div class="ageFilter" style="display: none; padding: 15px 0px 10px 0px">
        <a href="#" id="ageFilter_littleKids" class="btn btn-warning disabled" style="margin: 3px; pointer-events: auto">6 and under</a>
        <a href="#" id="ageFilter_middleKids" class="btn disabled" style="margin: 3px; pointer-events: auto; background-color: orange">7 to 11</a>
        <a href="#" id="ageFilter_bigKids" class="btn btn-info disabled" style="margin: 3px; pointer-events: auto">12 and older</a>
    </div>
    <div class="ratingsFilter" style="display: none; padding: 15px 0px 10px 0px">
        @foreach (range(4, 1) as $stars)
            <a href="#" id="ratingsFilter_{{ $stars }}" class="btn btn-warning disabled" style="margin: 3px; pointer-events: auto; background-color: white; border-color: white">
                {!! Helper::getRatingStars($stars) !!} & up
            </a>
        @endforeach
    </div>
    <div class="otherFilters" style="display: none; padding: 15px 0px 10px 0px">
        <a href="#" id="otherFilters_free_content" class="btn btn-secondary disabled" style="margin: 3px; pointer-events: auto">Show only FREE content</a>
    </div>
</div>

<script>
    var user_id = @json(Auth::check() ? Auth::user()->id : 0);

    // run this ONCE to wipe user's settings (get rid of old broken bits)
    if (localStorage.getItem('wipedSettings') === null) {
        localStorage.removeItem('filterSettings');
        localStorage.setItem('wipedSettings', true);
    }

    function saveFilter(filter, value) {
        if (localStorage.getItem('filterSettings') === null) {
            var filterArr = {};
        } else {
            var filterArr = JSON.parse(localStorage.getItem('filterSettings'));
        }
        filterArr[filter] = value;

        var filterSettingsString = JSON.stringify(filterArr);

        localStorage.setItem('filterSettings', filterSettingsString);
    }

    document.addEventListener('DOMContentLoaded', function() {

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
                    if ($(this).is(':visible')) {
                        $('#' + className).children().toggleClass('fa-caret-up fa-caret-down');
                        $(this).hide(300);
                    }
                } else {
                    // toggle the clicked one
                    $(this).toggle(300);
                }
            });
        });

        // attach event handlers to specific filter buttons ("a") to ensure correct disabling/enabling functionality
        $('.filters a').on('click', function(e){
            e.preventDefault();

            // find the relevant button
            var button = $(e.currentTarget);
            var id = button.attr('id');
            var filterIsOn = false;
            if (button.hasClass('disabled')) {
                // if it's disabled, reenable and remove blur
                setOtherFiltersOn(button);
                saveFilter(id, 'on');
            } else {
                // if it wasn't disabled, disable it but keep the pointer events (so it's still clickable)
                button.addClass('disabled').removeClass('btn-lg').css('pointer-events', 'auto').blur();
                saveFilter(id, 'off');
            }

            var parentDivClassName = button.parent().attr('class');

            $('.' + parentDivClassName + ' a').each(function(f){

                // disable the other buttons in the ratings
                if (parentDivClassName == "ratingsFilter") {
                    if ($(this).attr('id') !== id) {
                        $(this).addClass('disabled').removeClass('btn-lg');
                        saveFilter($(this).attr('id'), 'off');
                    }
                }

                if ($(this).hasClass('disabled') === false) {
                    filterIsOn = true;
                }
            });

            if (filterIsOn) {
                $('#' + parentDivClassName).attr('class', 'btn btn-success');
            } else {
                $('#' + parentDivClassName).attr('class', 'btn btn-outline-secondary disabled');
            }

            // if it's on the calendar page, refetch calendar events, otherwise it's the list view
            if (typeof calendar !== 'undefined') {
                calendar.refetchEvents();
            } else {
                drawTable();
            }
        });

        if ($('#search').length) {
            $('#search').on('keyup', function(e){
                if ($(e.currentTarget).val() === '') {
                    $("#searchBtn").attr('class', 'btn btn-outline-secondary disabled');
                    calendar.rerenderEvents();
                }
                if(e.keyCode === 13) {
                    e.preventDefault();
                    doSearch();
                }
            });

            // if the search box is used...
            $("#searchBtn").on('click', function(e){
                doSearch();
            });

            var doSearch = function(e) {
                var searchBtn = $("#searchBtn");
                if ($('#search').val() === '') {
                    if (searchBtn.hasClass('disabled')) {
                    } else {
                        searchBtn.attr('class', 'btn btn-outline-secondary disabled');
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
            }
        }

        // if the show favourites button is clicked
        $("#showFavourites").on('click', function(e){
            if (user_id) {
                var favouritesBtn = $(e.currentTarget);
                var heart = favouritesBtn.children();
                if (favouritesBtn.hasClass('disabled')) {
                    setFavouritesFilterOn(favouritesBtn, heart);
                    saveFilter('showFavourites', 'on');
                } else {
                    favouritesBtn.attr('class', 'btn btn-outline-secondary disabled');
                    heart.toggleClass('fas far').css('color', 'gray');
                    saveFilter('showFavourites', 'off');
                }
                if (typeof calendar !== 'undefined') {
                    calendar.refetchEvents();
                } else {
                    drawTable();
                }

            } else {
                var currentUrl = @json(base64_encode(url()->current()));
                axios.get('/api/loginWarning/show/filterFavourites/' + currentUrl)
                    .then(function (response) {
                        $('#loginModal').html(response.data).modal();
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            }
        });

        function setFavouritesFilterOn(favouritesBtn, heart){
            if (!favouritesBtn) {
                var favouritesBtn = $('#showFavourites');
                var heart = favouritesBtn.children();
            }
            favouritesBtn.attr('class', 'btn btn-success');
            heart.toggleClass('fas far').css('color', 'red');
        }

        function setOtherFiltersOn(button, id){
            if (!button) {
                var button = $('#' + id);
            }
            button.removeClass('disabled').addClass('btn-lg').blur();
            var parentDivClassName = button.parent().attr('class');
            $('#' + parentDivClassName).attr('class', 'btn btn-success');
        }

        setTimeout(() => {
            if (localStorage.getItem('filterSettings') !== null) {
                var filterSettings = JSON.parse(localStorage.getItem('filterSettings'));
                for(var i in filterSettings){
                    if (filterSettings[i] == "on" && $('#' + i)) {
                        if (i == "showFavourites") {
                            if (user_id) {
                                setFavouritesFilterOn();
                            }
                            // do nothing with favourites if user not logged in
                        } else {
                            setOtherFiltersOn(false, i);
                        }
                    }
                }
                // if it's on the calendar page, refetch calendar events, otherwise it's the list view
                if (typeof calendar !== 'undefined') {
                    calendar.refetchEvents();
                } else {
                    drawTable();
                }
            }
        }, 1500);

    });
</script>