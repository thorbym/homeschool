@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>
                Categories
            </h2>
            @if (Auth::check() && Auth::user()->isAdmin())
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="submit" class="btn btn-success" onclick="$('#editModal').modal()">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            @endif
            <br />
            @if ($categories)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Category</th>
                            <th scope="col">Colour</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <th scope="row">{{ $category->category }}</th>
                                <td><a href="#" id="{{ $category->category }}" class="btn" style="background-color: {{ $category->colour }}; color: {{ $category->font_colour }}">{{ $category->colour }}</a></td>
                            </tr>
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
<form action="{{ route('category') }}" method="POST">
    {{ csrf_field() }}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Category</h4>
                    <br />

                    <label for="title">Category</label>
                    <input type="text" class="form-control" name="category" id="category">
                    <br />

                    <label for="title">Colour</label>
                    <input type="text" class="form-control" name="colour" id="colour">
                    <br />

                    <label for="title">Font Colour</label>
                    <select class="form-control" name="font_colour" id="font_colour">
                        <option value="white">White</option>
                        <option value="black">Black</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

<script>

    document.addEventListener('DOMContentLoaded', function() {
        $('#colour').colorpicker();
    });

</script>