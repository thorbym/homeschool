@extends('layouts.app')

@section('content')

@include('layouts.navbar')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>
                Approve Reviews
            </h2>
            <br />
            @if ($events)
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Review Title</th>
                            <th>Review</th>
                            <th>Rating</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                            @php $reviews = $event->getNotApprovedRatings($event->id) @endphp
                            @if (isset($reviews[0]))
                                <tr style="background-color: #DEDEDE">
                                    <th colspan="5" scope="row">EVENT: {{ $event->title }}<br /><small>(Av. review {{ $event->average_rating }})</small></th>
                                </tr>
                                @foreach ($reviews as $review)
                                    <tr>
                                        <td>{{ $review->title }}</td>
                                        <td>{{ $review->body }}</td>
                                        <td>{{ $review->rating }}</td>
                                        <td><a href="{{ url('/review/approve/' . $review->id) }}" class="btn btn-primary btn-sm">Approve</a></td>
                                        <td><a href="{{ url('/review/delete/' . $review->id) }}" class="btn btn-danger btn-sm">Delete</a></td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <div>
                    No Categories
                </div>
            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
@endsection