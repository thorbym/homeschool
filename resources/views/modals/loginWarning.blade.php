<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header gray">
            <h4 class="modal-title">Please login</h4>
            <button type="button" class="close align-middle" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fas fa-times fa-lg align-middle" style="color: gray"></i></span>
            </button>
        </div>
        <div class="modal-body">
            <i class="fas fa-exclamation-circle fa-lg align-middle" style="color: red"></i>&nbsp To save your favourites, you will need to <a href="{{ route('register') }}">register</a>. If you have already registered, <a href="{{ route('login') }}">please login</a>.
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>