@extends('admin.layouts.before_auth_app')
@section('content')
<!-- メインコンテンツ-->
<div class="p-MK2001">
    <form id='login-form' name='form' method="POST" action="{{ route('admin.login') }}">
        @csrf
        <div class="p-MK2001__box u-mb20">
            <div class="p-MK2001__head u-align--center u-mb25">
                <h1 class="p-MK2001__hdg u-mb20">{{ trans('generalConst.ADMIN_APP_NAME') }}</h1>
                <p>{{ trans('messages.management_system') }}</p>
            </div>
            <div class="p-MK2001__content">
                <input type="hidden" name="time_zone" id="time-zone"/>
                <input
                    class="p-MK2001__input c-input --max @error('login_id') is-error @enderror u-mb20"
                    type="text"
                    name="login_id"
                    value="{{ old('login_id') }}"
                    placeholder="{{ trans('messages.user_id') }}"
                />
                <input
                    class="p-MK2001__input c-input --max @error('password') is-error @enderror u-mb20"
                    type="password"
                    name="password"
                    placeholder="{{ trans('messages.password') }}"
                />
                @if($errors->any())
                <div class="card-text text-left alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li class="u-color --warning u-fs12 u-fw--bold u-mb5">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <button class="p-MK2001__submit c-btn u-mt15" id="submit-login" type="submit">{{ trans('messages.login') }}</button>
            </div>
        </div>
    </form>
    <p class="p-MK2001__link p-MK2001__link--forget">
        <a class="u-link --color" href="{{ route('admin.passwordReset.index') }}">{{ trans('messages.forgot_password') }}</a>
    </p>
</div>
<!-- ./メインコンテンツ -->
@endsection
@section('script')
@parent
<script src="{{ asset('js/admin/login.js'.jsCssForceReload()) }}" defer></script>
@endsection
