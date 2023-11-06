@extends('admin.layouts.before_auth_app')
@section('content')
    <!-- メインコンテンツ-->
    <div class="p-MK2001">
        <div class="p-MK2001__box u-mb50">
            <div class="p-MK2001__head u-align--center u-mb25">
                <h1 class="p-MK2001__hdg u-mb20">{{ trans('generalConst.ADMIN_APP_NAME') }}</h1>
                <p>{{ trans('messages.management_system') }}</p>
            </div>
            <div class="p-MK2001__content u-mt50">
                <p class="u-align--center u-mb20">{{ trans('generalConst.password_sent') }}</p>
                <a class="p-MK2001__submit c-btn u-mt50" href="{{ route('admin.login') }}">{{ trans('generalConst.back_login') }}
                </a>
            </div>
        </div>
    </div>
    <!-- ./メインコンテンツ -->
@endsection
