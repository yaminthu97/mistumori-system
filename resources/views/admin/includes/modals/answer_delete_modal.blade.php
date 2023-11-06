<div class="c-modal c-modal--dialog js-modal" id="js-answer-delete-modal">
    <form id="answer-delete-form-data" action="{{ route('admin.answer.delete') }}" method="POST">
        @csrf
        <input type="hidden" id="aID" name="answer_id">
        <input type="hidden" id="cuID" name="created_user_id">
        <div class="c-modal-container">
            <div class="c-modal-header">
                <h4 class="comment-modal-header">{{ trans('messages.answer_delete') }}</h4>
                <button type="button" class="comment-modal-close js-modal-btn--close" data-dismiss="modal"
                    aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h4 class="c-modal-header__title --dialog u-mt50 u-mb45 u-fw--normal">{{ trans('messages.answer_delete_alert') }}</h4>
            <div class="c-modal-body">
                <ul class="c-modal-btns">
                    <li class="c-modal-btns__item --dialog"><a
                            class="c-btn --frame --bl js-modal-btn--close cancel-btn-border">{{ trans('messages.cancel') }}</a></li>
                    <li class="c-modal-btns__item --dialog"><button class="c-btn --bl" type="submit"
                            id="js-answer-delete-modal-btn">{{ trans('messages.delete') }}</button></li>
                </ul>
            </div>
        </div>
    </form>
</div>
