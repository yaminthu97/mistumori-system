@extends('admin.layouts.app')
@section('content')
    <div class="l-inner">
        <ul class="c-breadcrumb">
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="{{ route('admin.top.index') }}">{{ trans('messages.top') }}</a>
            </li>
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="{{ route('admin.account.index') }}">{{ trans('generalConst.ACCOUNT_MANAGEMENT') }}</a>
            </li>
            <li class="c-breadcrumb__item">
                <span class="c-breadcrumb__link">
                    {{ empty($account['id']) ? trans('messages.new_create') : trans('messages.edit') }}
                </span>
            </li>
        </ul>
        <h1 class="u-heading-h1 u-mb20">
            {{ empty($account['id']) ? trans('messages.new_account_add') : trans('messages.account_edit') }}
        </h1>
        <section class="p-MK02013 u-mb40">

            <form action="{{ route('admin.account.save', ['admin_account_id' => $account['id'] ?? null]) }}" method="POST"
                id="account_info_form" enctype="multipart/form-data">
                @csrf
                <table class="c-table u-mb15">
                    <tbody class="c-table-body">
                        <input type="hidden" name="complete_flg" id="complete_flg"
                            data-url="{{ route('admin.account.index') }}" value="{{ $complete_flg }}">
                        <input type="hidden" name="mail_failure" id="mail_failure"
                            value="{{ session()->get('mail_failure') ?? false }}">
                        <input type="hidden" name="account_id" value="{{ $account['id'] ?? '' }}">
                        <input type="hidden" name="confirm" value="{{ session()->get('confirm') ?? false }}" />
                        <tr class="c-table__tr">
                            <th class="c-table__th --w20pct --leftTop">{{ trans('messages.user_id') }}<br>{{ trans('messages.user_email') }}</th>
                            <td class="c-table__td --rightTop">
                                <input class="c-input" type="text" name="email"
                                    value="{{ old('email', $account['email'] ?? '') }}" placeholder="tanakataro@au.com">
                                @error('email')
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mb15 " data-error-name="email">
                                        {{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        <tr class="c-table__tr">
                            <th class="c-table__th --w20pct">{{ trans('messages.name') }}</th>
                            <td class="c-table__td">
                                <input class="c-input" type="text" name="name"
                                    value="{{ old('name', $account['name'] ?? '') }}" placeholder="田中太郎">
                                @error('name')
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mb15" data-error-name="name">
                                        {{ $message }}</p>
                                @enderror
                            </td>
                        </tr>

                        @php
                            $admin_session_data = session()->get('mk_admin_session');
                        @endphp
                        <tr class="c-table__tr">
                            <th class="c-table__th --w20pct">{{ trans('messages.role') }}</th>
                            <td class="c-table__td">
                                <label class="c-select --w220">
                                    <select name="role" class="c-select__item js-switch-select">
                                        @foreach (GeneralConst::ROLE_LIST as $key => $value)
                                            <option @if (old('role', $account['role_id'] ?? '') == $key) selected @endif
                                                value="{{ $key }}">{{ trans('generalConst.role_list.' . $key) }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                @error('role')
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mb15" data-error-name="role">
                                        {{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- モーダルコンテンツ -->
                <div class="c-modal-overlay js-modal-save-overlay"></div>
                <div class="c-modal c-modal--dialog js-modal" id="js-modal-save">
                    <div class="c-modal-container">
                        <h4 class="c-modal-header__title --dialog u-mt50 u-mb45 u-fw--normal">{{ trans('messages.account_register_alert') }}</h4>
                        <ul class="c-modal-btns">
                            <li class="c-modal-btns__item --dialog"><a
                                    class="c-btn --frame --bl js-modal-btn--close">{{ trans('messages.cancel') }}</a></li>
                            <li class="c-modal-btns__item --dialog"><a class="c-btn --bl"
                                    id="js-modal-account-save-btn">{{ trans('messages.register') }}</a></li>
                        </ul>
                    </div>
                </div>

                <!-- ./ モーダルコンテンツ -->
                <div class="c-modal-overlay js-modal-complete-overlay"></div>
                <div class="c-modal c-modal--dialog js-modal" id="js-modal-complete">
                    <div class="c-modal-container">
                        <h4 class="c-modal-header__title --dialog u-mt50 u-mb45 u-fw--normal">{{ trans('messages.saved') }}</h4>
                        <ul class="c-modal-btns">
                            <li class="c-modal-btns__item --dialog">
                                <a class="c-btn --bl js-complete-btn">OK</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </section>
        <div class="u-align--left">
            <a class="c-btn --frame --bl u-mr20 back-btn" href="{{ route('admin.account.index') }}" id="js-account-cancel">{{ trans('messages.back') }}</a>
            <button class="c-btn --bl js-form-submit" id="js-account-save">{{ trans('messages.register') }}</button>
        </div>
    </div>
    @include('admin.includes.modals.account_delete_modal')
    @include('admin.includes.modals.account_mail_failure')
@endsection

@section('script')
    @parent
    <script src="{{ asset('js/admin/account.js' . jsCssForceReload()) }}" defer></script>
@endsection
