<nav class="c-sidebar" id="sidebar">
    <div class="c-sidebar-nav">
        @php
            $admin_session_data = session()->get('mk_admin_session');
        @endphp
        <ul class="c-sidebar-nav__list">
            @if ($admin_session_data['role_id'] == GeneralConst::SALES || $admin_session_data['role_id'] == GeneralConst::MTM)
                <li class="c-sidebar-nav__item MKCUSTOMER">
                    <a class="c-sidebar-nav__link  @if ($admin_session_data['menu_index'] == GeneralConst::ADMIN_MENU_CUSTOMER_MANAGEMENT) is-current @endif"
                        href="{{ route('admin.customer.index') }}">
                        <span class="c-sidebar-nav__icon c-sidebar-nav__icon--customer">&nbsp;</span>{{ trans('generalConst.CUSTOMER_MANAGEMENT') }}
                    </a>
                </li>
            @endif
            @if ($admin_session_data['role_id'] == GeneralConst::SALES || $admin_session_data['role_id'] == GeneralConst::MTM)
                <li class="c-sidebar-nav__item MKPROJECT">
                    <a class="c-sidebar-nav__link @if ($admin_session_data['menu_index'] == GeneralConst::ADMIN_MENU_PROJECT_MANAGEMENT) is-current @endif"
                        href="{{ route('admin.project.index') }}">
                        <span class="c-sidebar-nav__icon c-sidebar-nav__icon--project">&nbsp;</span>{{ trans('generalConst.PROJECT_MANAGEMENT') }}
                    </a>
                </li>
            @endif
            @if ($admin_session_data['role_id'] == GeneralConst::SALES || $admin_session_data['role_id'] == GeneralConst::MTM)
                <li class="c-sidebar-nav__item MKINQUIRY">
                    <a class="c-sidebar-nav__link  @if ($admin_session_data['menu_index'] == GeneralConst::ADMIN_MENU_INQUIRY_MANAGEMENT) is-current @endif"
                        href="{{ route('admin.inquiry.index') }}">
                        <span class="c-sidebar-nav__icon c-sidebar-nav__icon--question">&nbsp;</span>{{ trans('generalConst.INQUIRY_MANAGEMENT') }}
                    </a>
                </li>
            @endif
            @if ($admin_session_data['role_id'] == GeneralConst::SALES || $admin_session_data['role_id'] == GeneralConst::MTM)
                <li class="c-sidebar-nav__item MKACCOUNT">
                    <a class="c-sidebar-nav__link @if ($admin_session_data['menu_index'] == GeneralConst::ADMIN_MENU_ACCOUNT_MANAGEMENT) is-current @endif"
                        href="{{ route('admin.account.index') }}">
                        <span class="c-sidebar-nav__icon c-sidebar-nav__icon--account">&nbsp;</span>{{ trans('generalConst.ACCOUNT_MANAGEMENT') }}
                    </a>
                </li>
            @endif
            @if ($admin_session_data['role_id'] == GeneralConst::SALES || $admin_session_data['role_id'] == GeneralConst::MTM)
                <li class="c-sidebar-nav__item MKWIKI">
                    {{-- // TODO:: To add index route --}}
                    <a class="c-sidebar-nav__link @if ($admin_session_data['menu_index'] == GeneralConst::ADMIN_MENU_WIKI) is-current @endif"
                        href="{{ route('admin.wiki.index') }}">
                        <span class="c-sidebar-nav__icon c-sidebar-nav__icon--wiki">&nbsp;</span>{{ trans('generalConst.WIKI') }}
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>
