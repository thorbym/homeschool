<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header gray">
            <h4 class="modal-title">Login or Register</h4>
            <button type="button" class="close align-middle" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fas fa-times fa-lg align-middle" style="color: gray"></i></span>
            </button>
        </div>
        <div class="modal-body">
            <br />
            <p>
                To become a contributor, you will need to <a href="{{ route('register', ['p' => $package]) }}">register</a>.
            </p>
            <p>
                If you've already registered, please <a href="{{ route('login', ['p' => $package]) }}">login here</a>.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>