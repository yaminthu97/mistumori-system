<!-- モーダル-->
<!-- モーダル オーバーレイ-->
<div class="c-modal-overlay js-modal-overlay"></div>
<!-- ツールチップ用オーバーレイ-->
<div class="c-modal-overlay --tooltip js-modal-tooltip-overlay"></div>
<!-- ./ モーダル-->
<!-- モーダルコンテンツ（ログアウト）-->
@php
$admin_login = session()->get('mk_admin_session')
@endphp
<div class="c-modal c-modal--logout c-modal--profile js-modal" id="js-modal-logout">
    <div class="c-modal-container">
        <ul class="dropdown-menu">
            <li class="dropdown-menu__item user-profile__name icon--user">{{$admin_login['name']}}</li>
            <li class="dropdown-menu__item icon--user-guide">
                <a class="dropdown-menu__link" href="">ユーザーガイド</a>
            </li>
            <li class="dropdown-menu__item is_border icon--setting">
                <a class="dropdown-menu__link" href="{{ route('admin.change.language') }}">{{ trans('messages.setting')}}</a>
            </li>
        </ul>
        <form id='logout-form' name='form' method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="c-modal__btn logout__btn">{{ trans('messages.logout')}}</button>
        </form>
    </div>
</div>
<!-- ./ モーダルコンテンツ（ログアウト）-->
