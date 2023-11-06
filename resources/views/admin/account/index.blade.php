@extends('admin.layouts.app')
@section('content')
    <div class="l-inner">
        <ul class="c-breadcrumb">
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="{{ route('admin.top.index') }}">{{ trans('messages.top') }}</a></li>
            <li class="c-breadcrumb__item"><span class="c-breadcrumb__link">{{ trans('generalConst.ACCOUNT_MANAGEMENT') }}</span></li>
        </ul>
        <h1 class="u-heading-h1 u-mb20">{{ trans('generalConst.ACCOUNT_MANAGEMENT') }}</h1>

        <form action="{{ route('admin.account.index') }}" method="GET">
            <div class="c-box --frame --search u-mb20 account-search-frame">
                <div class="c-box__input account-search">
                    <div class="">
                        <input class="c-input u-mr20" type="text" name="name" placeholder="{{ trans('messages.name') }}"
                            value="{{ old('name', $search_info['name'] ?? '') }}">
                        @error('name')
                            <p class="u-color --warning u-fs12 u-fw--bold u-mb15 account-search-item" data-error-name="name">
                                {{ $message }}</p>
                        @enderror
                    </div>
                    <div class="">
                        <input class="c-input" type="text" name="email" placeholder="{{ trans('messages.user_id') }}{{ trans('messages.user_email') }}"
                            value="{{ old('email', $search_info['email'] ?? '') }}">
                        @error('email')
                            <p class="u-color --warning u-fs12 u-fw--bold u-mb15 account-search-item" data-error-name="email">
                                {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="c-box__btns">
                    <a class="c-btn --frame --search u-mr20" href="{{ route('admin.account.index') }}">{{ trans('messages.clear') }}</a>
                    <button class="c-btn --search" type="submit" id="account-search">{{ trans('messages.search') }}</button>
                </div>
            </div>
        </form>

        @php
            $admin_login = session()->get('mk_admin_session');
        @endphp

        <form id="account_list_form" action="{{ route('admin.account.deleteSelected') }}" method="post">
            @csrf
            <section class="p-MK02013">
                <div class="c-clm--between @if (!$check_flg || $errors->any()) create-btn-position @endif">
                    @if ($check_flg && !$errors->any())
                        <p class="total-count">{{ trans('messages.search_result') }}：<span class="u-fw--bold">{{ $total_accounts }}</span> {{ trans('messages.item') }}</p>
                    @endif
                    <div class="u-align--right">
                        <a class="c-btn --bl --variable u-ml40 u-link --add2 account-add"
                            href="{{ route('admin.account.save') }}">{{ trans('messages.new_create') }}</a>
                    </div>
                </div>

                @if (isset($exceededDataLimit))
                    <div class="data-limit-error text-left alert alert-danger">
                        <ul class="mb-0">
                            <li class="u-color --warning u-fs12 u-fw--bold u-mb5">{{ $exceededDataLimit }}</li>
                        </ul>
                    </div>
                @endif

                @if ($check_flg && !$errors->any())
                    <table class="c-table">
                        <thead class="c-table-head">
                            <tr class="c-table__tr">
                                <th class="c-table__th p-MK02013-table__th --thead --checkbox --leftTop">
                                    <label class="c-checkbox">
                                        <input class="c-checkbox__input" type="checkbox" name="check_all" id="check_all"
                                            @if ($total_accounts === 0) disabled @endif>
                                        <span class="c-checkbox__label"></span>
                                    </label>
                                </th>
                                <th class="c-table__th p-MK02013-table__th --thead">{{ trans('messages.name') }}</th>
                                <th class="c-table__th p-MK02013-table__th --thead">{{ trans('messages.user_id') }}{{ trans('messages.user_email') }}</th>
                                <th class="c-table__th p-MK02013-table__th --thead">{{ trans('messages.role') }}</th>
                                <th class="c-table__th p-MK02013-table__th --thead --w12pct --rightTop">&nbsp;</th>
                            </tr>
                        </thead>
                        <input type="hidden" id="account_data" name="account_data" value="" />
                        <input type="hidden" id="account" name="account" value="{{ json_encode($accounts) }}" />
                        <tbody class="c-table-body">
                            @foreach ($accounts as $account)
                                <tr class="c-table__tr">
                                    <td class="c-table__td p-MK02013-table__td --checkbox --leftBottom">

                                        <label class="c-checkbox">
                                            <input class="c-checkbox__input row_checkbox" type="checkbox"
                                                name="row_checkbox[]" value="{{ $account->id }}"
                                                @if ($admin_login['id'] === $account->id) disabled @endif />
                                            <span class="c-checkbox__label"></span>
                                        </label>
                                    </td>
                                    <td class="c-table__td p-MK02013-table__td">{{ $account->name }}</td>
                                    <td class="c-table__td p-MK02013-table__td">{{ $account->email }}</td>
                                    <td class="c-table__td p-MK02013-table__td">
                                        {{ trans('generalConst.role_list.' . $account->role) }}</td>
                                    <td class="c-table__td p-MK02013-table__td --rightBottom">
                                        <a class="c-btn p-MK02013-btn --edit"
                                            href="{{ route('admin.account.save', [$account->id]) }}">{{ trans('messages.edit') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </section>
        </form>
    </div>

    <!-- モーダルコンテンツ（MK02013 削除）-->
    <div class="c-modal c-modal--dialog js-modal" id="js-account-delete-modal">
        <div class="c-modal-container">
            <h4 class="c-modal-header__title --dialog u-mt50 u-mb45 u-fw--normal">{{ trans('messages.account_delete') }}</h4>
            <ul class="c-modal-btns">
                <li class="c-modal-btns__item --dialog"><a
                        class="c-btn --frame --bl js-modal-btn--close cancel-btn-border">{{ trans('messages.cancel') }}</a></li>
                <li class="c-modal-btns__item --dialog"><a class="c-btn --bl" id="js-account-delete-modal-btn">{{ trans('messages.delete') }}</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="c-modal-overlay js-modal-csv-upload-overlay"></div>
    <div class="c-modal-overlay js-modal-csv-download-overlay"></div>
    @include('admin.includes.modals.appropriating_data_download_modal')
@endsection

@section('page_modal')
    @include('admin.includes.modals.account_csv_upload_modal')
    @include('admin.includes.modals.account_csvmsg_modal')
@endsection
@section('script')
    @parent
    <script src="{{ asset('js/admin/account.js' . jsCssForceReload()) }}" defer></script>
    <script src="{{ asset('js/admin/account_csv_upload.js' . jsCssForceReload()) }}" defer></script>
@endsection
