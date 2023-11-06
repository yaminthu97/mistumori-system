<header class="c-header" id="header">
    <h1 class="c-header__txt">{{ trans('generalConst.ADMIN_APP_NAME') }}</h1>
    @php
        $admin_login = session()->get('mk_admin_session');
    @endphp
    <div class="c-header-primary-nav">
        <button class="dark-mode-toggle">
            <span class="toggle-switch">
                <span class="toggle-switch-icon">
                    <img class="toggle-switch-sun" src="{{ asset('img/admin/common/ico_sun.svg') }}" alt="sun" />
                    <img class="toggle-switch-moon" src="{{ asset('img/admin/common/ico_moon.svg') }}" alt="moon" />
                </span>
            </span>
        </button>

        <form action="{{ route('admin.notification.notificationList') }}" method="get" data-id="notification-id">
            <div id="notification" class="openbtn">
                <div class="notification-icon">
                    <img src="{{ asset('img/admin/common/icon-notification-bell.svg') }}" class="noti-img" alt="Notification_Bell">
                </div>
            </div>
        </form>

        <div class="noti-sidebar" style="display:none" id="noti-side-panel">
            <div class="noti-close-btn">
                <h4>通知</h4>
                <a href="javascript:void(0)" class="closebtn-icon" onclick="closeNav()">&times;</a>
            </div>

            <div id="notification-list">
            </div>
        </div>

        <a class="c-header-primary-nav__profile js-modal-btn--open" data-modal="js-modal-btn-logout">
            <span class="c-header-primary-nav__name">{{ $admin_login['name'] }}</span>
            <img class="c-header-primary-nav__icon" src="asset('img/admin/common/icon-user.png') }}"
                srcset="{{ asset('img/admin/common/icon-user.png') }} 1x, {{ asset('img/admin/common/icon-user@2x.png') }} 2x"
                alt="あいおい太郎" />
        </a>
    </div>
</header>

@section('script')
    <script src="{{ asset('js/admin/darkmode.js' . jsCssForceReload()) }}"></script>
    <script src="{{ asset('js/admin/pusher.min.js' . jsCssForceReload()) }}"></script>
    <script src="{{ asset('js/admin/luxon.min.js') }}"></script>
    <script src="{{ asset('js/admin/notification.js' . jsCssForceReload()) }}" defer></script>
    <script src="{{ asset('js/admin/onboarding.js' . jsCssForceReload()) }}" defer></script>
    <script>
        const PROJECT_CREATE = @json(GeneralConst::PROJECT_CREATE, JSON_PRETTY_PRINT);
        const QUESTION_CREATE = @json(GeneralConst::INQUIRY_CREATE, JSON_PRETTY_PRINT);
        const PROJECT_UPDATE = @json(GeneralConst::PROJECT_UPDATE, JSON_PRETTY_PRINT);
        const QUESTION_UPDATE = @json(GeneralConst::INQUIRY_UPDATE, JSON_PRETTY_PRINT);
        const COMMENT_CREATE = @json(GeneralConst::COMMENT_CREATE, JSON_PRETTY_PRINT);
        const COMMENT_EDIT = @json(GeneralConst::COMMENT_EDIT, JSON_PRETTY_PRINT);
        const ESTIMATE_CREATE = @json(GeneralConst::ESTIMATE_CREATE, JSON_PRETTY_PRINT);
        const ESTIMATE_UPDATE= @json(GeneralConst::ESTIMATE_UPDATE, JSON_PRETTY_PRINT);
        const ANSWER_CREATE = @json(GeneralConst::ANSWER_CREATE, JSON_PRETTY_PRINT);
        const ANSWER_UPDATE = @json(GeneralConst::ANSWER_UPDATE, JSON_PRETTY_PRINT);
        const CREATE_TEXT = @json(GeneralConst::CREATE_TEXT, JSON_PRETTY_PRINT);
        const UPDATE_TEXT = @json(GeneralConst::UPDATE_TEXT, JSON_PRETTY_PRINT);
        const TIMEZONE = @json($admin_login, JSON_PRETTY_PRINT);
        const STATUS = @json(GeneralConst::PROJECT_STATUS, JSON_PRETTY_PRINT);
        const PUSHER_KEY = @json(env('PUSHER_APP_KEY'), JSON_PRETTY_PRINT);
        const PUSHER_APP_CLUSTER = @json(env('PUSHER_APP_CLUSTER'), JSON_PRETTY_PRINT);
    </script>
@endsection
