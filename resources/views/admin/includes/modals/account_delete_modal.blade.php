<!-- モーダルコンテンツ（MK02013 削除確認）-->
<div class="c-modal c-modal--dialog js-modal" id="js-account-delete-modal">
    <div class="c-modal-container">
        <form action="{{ route('admin.account.delete') }}" method="POST">
            @csrf
            <h4 class="c-modal-header__title --dialog u-mt50 u-mb45 u-fw--normal">{{ trans('messages.account_delete') }}</h4>
            <input type="hidden" name="account_id" value="{{ $account['id'] ?? '' }}">
            <ul class="c-modal-btns">
                <li class="c-modal-btns__item --dialog">
                    <a class="c-btn --frame --bl js-modal-btn--close">{{ trans('messages.cancel') }}</a>
                </li>
                <li class="c-modal-btns__item --dialog">
                    <button type="submit" class="c-btn --bl">{{ trans('messages.delete') }}</button>
                </li>
            </ul>
        </form>
    </div>
</div>
<!-- ./ モーダルコンテンツ（MK02013 削除確認）-->
