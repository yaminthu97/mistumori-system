@extends('admin.layouts.before_auth_app')
@section('content')
    <!-- メインコンテンツ -->
    <div class="p-MK2001">
        <form id='login-form' name='form' action="{{ route('admin.passwordReset.create') }}" method="POST">
            @csrf
            <div class="p-MK2001__box u-mb50">
                <div class="p-MK2001__head u-align--center u-mb25">
                    <h1 class="p-MK2001__hdg u-mb20">{{ trans('generalConst.ADMIN_APP_NAME') }}</h1>
                    <p>{{ trans('messages.management_system') }}</p>
                </div>
                <div class="p-MK2001__content u-mt50">
                    <p class="-cancel u-align--center u-mb20">{{ trans('messages.reset_email') }}</p>
                    <input class="p-MK2001__input c-input --max @error('email') is-error @enderror u-mb20" type="text"
                        name="email" value="{{ old('email') }}" placeholder="{{ trans('messages.email') }}">

                    <input type="hidden" name="mail_failure" id="mail_failure"
                        value="{{ session()->get('mail_failure') ?? false }}">

                    @if ($errors->any())
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li class="u-color --warning u-fs12 u-fw--bold u-mb15">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <button class="p-MK2001__submit c-btn u-mt10" type="submit">{{ trans('messages.password_reset') }}</button>
                </div>
            </div>
        </form>
        <p class="p-MK2001__link back-btn"><a href="{{ route('admin.login') }}">← {{ trans('messages.back') }}</a></p>
    </div>
    @include('admin.includes.modals.account_mail_failure')
@endsection

@section('script')
    @parent
    <script src="{{ asset('js/admin/mail-failure.js' . jsCssForceReload()) }}" defer></script>
@endsection
