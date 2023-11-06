@extends('admin.layouts.app')
@section('content')
    <div class="l-inner">
        <ul class="c-breadcrumb">
            {{-- TODO::To add top page route --}}
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="">{{ trans('messages.top') }}</a></li>
            <li class="c-breadcrumb__item"><span class="c-breadcrumb__link">{{ trans('generalConst.CUSTOMER_MANAGEMENT') }}</span></li>
        </ul>
        <h1 class="u-heading-h1 u-mb20">{{ trans('generalConst.CUSTOMER_MANAGEMENT') }}</h1>
        <form class="MKCUSTOMER" action="{{ route('admin.customer.index') }}" method="GET">
            <div class="c-box --frame --search customer-search-frame u-mb30">
                <div class="c-box__input c-clm u-mb30">
                    <div class="form-control">
                        <label class="c-form__item c-label"><span class="c-form__label --align">{{ trans('messages.customer_name') }} :</span></label>
                        <input class="c-input --w220 u-mr30" type="text" name="customer_name" value="{{ old('customer_name', $search_info['customer_name'] ?? '') }}" autocomplete="off">
                        @error('customer_name')
                            <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15" data-error-name="customer_name">
                                {{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-control">
                        <label class="c-form__item c-label"><span class="c-form__label --align">{{ trans('messages.status') }} :</span></label>
                        <div class="c-select --w220">
                            <select name="status" class="c-select__item js-switch-select" autocomplete="off">
                                <option value="" selected></option>
                                @foreach (GeneralConst::CUSTOMER_STATUS as $key => $value)
                                    <option @if (old('status', $search_info['status'] ?? '') === (string) $key) selected @endif
                                        value="{{ $key }}">{{ trans('generalConst.customer_status.' . $key) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('status')
                            <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15" data-error-name="status">
                                {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="c-box__btns">
                    <a class="c-btn --frame --search u-mr20" href="{{ route('admin.customer.index') }}">{{ trans('messages.clear') }}</a>
                    <button class="c-btn --search" type="submit">{{ trans('messages.search') }}</button>
                </div>
            </div>
        </form>

        <section class="p-MK02013 MKCUSTOMER">
            <div class="c-clm--between @if ($errors->any()) create-btn-position @endif">
                @if (!$errors->any())
                    <p class="total-count">{{ trans('messages.search_result') }}：<span class="u-fw--bold">{{ count($customers) }}</span> {{ trans('messages.item') }}</p>
                @endif
                <div class="u-align--right">
                    <a href="{{ route('admin.customer.save') }}"
                        class="c-btn --bl --variable u-ml40 u-link --add2 customer-add">{{ trans('messages.new_create') }}</a>
                </div>
            </div>

            @if (!$errors->any())
                <table class="c-table customer-tbl u-mt20">
                    <thead class="c-table-head">
                        <tr class="c-table__tr">
                            <th class="c-table__th p-MK02013-table__th --thead">{{ trans('messages.customer_name') }}</th>
                            <th class="c-table__th p-MK02013-table__th --thead">{{ trans('messages.status') }}</th>
                            <th class="c-table__th p-MK02013-table__th --thead --w12pct --rightTop">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody class="c-table-body">
                        @if (count($customers) < 1)
                        <tr>
                            <td class="c-table__td p-MK02013-table__td u-align--center" colspan="3">{{ trans('messages.no_result') }}
                            </td>
                        </tr>
                        @else
                            @foreach ($customers as $customer)
                                <tr class="c-table__tr">
                                    <td class="c-table__td p-MK02013-table__td">{{ $customer->customer_name }}</td>
                                    <td class="c-table__td p-MK02013-table__td">
                                        {{ trans('generalConst.customer_status.' . $customer->status) }}
                                    </td>
                                    <td class="c-table__td p-MK02013-table__td --rightBottom">
                                        <a href="{{ route('admin.customer.save', [$customer->id]) }}"
                                            class="c-btn p-MK02013-btn --edit">{{ trans('messages.edit') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            @endif
        </section>

    </div>
@endsection
