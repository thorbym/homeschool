<div class="row justify-content-center" style="margin-bottom: 10px">
    <div class="col-md-1">
    </div>
    <div class="col-md-8">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ last(request()->segments()) == 'list' || last(request()->segments()) == 'quickStart' ? 'active' : '' }}" href="{{ route('list') }}">List</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ last(request()->segments()) == 'calendar' ? 'active' : '' }}" href="{{ route('calendar') }}">Calendar</a>
            </li>
            @if (Auth::check() && Auth::user()->isAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ last(request()->segments()) == 'categories' ? 'active' : '' }}" href="{{ url('categories') }}">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('createEvent') }}">Add Event</a>
                </li>
            @endif
        </ul>
    </div>
    <div class="col-md-3">
    </div>
</div>