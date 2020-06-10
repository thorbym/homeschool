@extends('layouts.app')

@section('content')

@include('layouts.navbar')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>
                Categories
            </h2>
            @if (Auth::check() && Auth::user()->isAdmin())
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            @endif
            <br />
            @if ($categories)
                <table class="table table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Category</th>
                            <th scope="col">Colour</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr id="{{ $category->id }}">
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
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
@endsection

<script>

    var isAdmin = @json(Auth::check() ? Auth::user()->isAdmin() : 0);

    document.addEventListener('DOMContentLoaded', function() {

        $('button').on('click', function(){
            if (isAdmin) {
                axios.get('/api/category/create')
                    .then(function (response) {
                        $('#categoryModal').html(response.data).modal();
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            }
        });

        $('table tbody tr th,td').on('click', function(e){
            var id = $(e.currentTarget).closest('tr').attr('id');
            if (isAdmin) {
                axios.get('/api/category/' + id + '/edit')
                    .then(function (response) {
                        $('#categoryModal').html(response.data).modal();
                    })
                    .catch(function (error) {
                        console.log(error);
                });
            }
        });

    });



</script>