<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header gray">
            <h4 class="modal-title">Login to submit content</h4>
            <button type="button" class="close align-middle" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fas fa-times fa-lg align-middle" style="color: gray"></i></span>
            </button>
        </div>
        <div class="modal-body">
            <p>
                We'd love you to submit content for TeachEm!
            </p>
            <p>
                However, our moderators will need to make sure all content is suitable and reaches the high standards that our users expect.
            </p>
            <i class="fas fa-exclamation-circle fa-lg align-middle" style="color: red"></i>&nbsp To submit content for consideration by our moderators, you will need to <a href="{{ route('register') }}">register</a>. If you have already registered, <a href="{{ route('login') }}">please login</a> and try this button again.
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>