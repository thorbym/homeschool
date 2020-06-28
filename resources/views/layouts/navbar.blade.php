@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row justify-content-center" style="margin-bottom: 10px">
    <div class="col-md-1">
    </div>
    <div class="col-md-8">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ last(request()->segments()) == 'calendar' ? 'active' : '' }}" href="{{ route('calendar') }}">Scheduled events</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ last(request()->segments()) == 'list' ? 'active' : '' }}" href="{{ route('list') }}">All events</a>
            </li>
            @if (Auth::check() && Auth::user()->isAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ last(request()->segments()) == 'categories' ? 'active' : '' }}" href="{{ url('categories') }}">Categories</a>
                </li>
            @endif
        </ul>
    </div>
    <div class="col-md-3">
    </div>
</div>