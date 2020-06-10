<div class="modal-dialog" role="document">
    @if ($category)
        <form action="{{ url('/category/' . $category->id) }}" method="POST">
        @method('PATCH')
    @else
        <form action="{{ url('/category') }}" method="POST">
    @endif
    @csrf
        <div class="modal-content">
            <div class="modal-body">
                <h4>{{ $category ? $category->category : 'New category' }}</h4>
                <br />
                <div class="form-group">
                    <label for="category">Category</label>
                    <input required type="text" class="form-control" name="category" id="category" value="{{ $category ? $category->category : '' }}">
                </div>

                <div class="form-group">
                    <label for="colour">Colour</label>
                    <input type="text" class="form-control" name="colour" id="colour" value="{{ $category ? $category->colour : '' }}">
                    <br />
                </div>

                <div class="form-group">
                    <label for="title">Font Colour</label>
                    <select class="form-control" name="font_colour" id="font_colour">
                        <option value="white" {{ $category && $category->font_colour == 'white' ? 'selected' : '' }}>White</option>
                        <option value="black" {{ $category && $category->font_colour == 'black' ? 'selected' : '' }}>Black</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
                @if (Auth::check() && Auth::user()->isAdmin())
                    @if ($category)
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

        $('#colour').colorpicker();

    });

    var changeMethod = function () {
        if (confirm('Are you SURE you want to delete?')) {
            $('form input[name="_method"]').val('DELETE').submit();
        }
    }

</script>