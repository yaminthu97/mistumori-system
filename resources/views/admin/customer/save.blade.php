@extends('admin.layouts.app')
@section('content')
    <div class="l-inner">
        <ul class="c-breadcrumb">
            {{-- TODO:: To add top page route --}}
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="">{{ trans('messages.top') }}</a></li>
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="{{ route('admin.customer.index') }}">{{ trans('generalConst.CUSTOMER_MANAGEMENT') }}</a>
            </li>
            <li class="c-breadcrumb__item">
                <span class="c-breadcrumb__link">
                    {{ empty($customer['id']) ? trans('messages.customer_create') :  trans('messages.customer_edit') }}
                </span>
            </li>
        </ul>
        <h1 class="u-heading-h1 u-mb20">
            {{ empty($customer['id']) ? trans('messages.customer_create') : trans('messages.customer_edit') }}
        </h1>

        <section class="p-MK02013 MKCUSTOMER u-mb40">
            <div class="c-section-header__title c-clm--between u-mb20">
                <h2 class="u-heading-h3">{{ trans('messages.customer_info') }}</h2>
            </div>
            <form action="{{ route('admin.customer.save', ['id' => $customer['id'] ?? null]) }}" method="POST"
                id="customer_info_form" enctype="multipart/form-data">
                @csrf
                <table class="c-table --noborder u-mb15">
                    <tbody class="c-table-body">
                        <input type="hidden" name="customer_id" value="{{ $customer['id'] ?? '' }}">
                        <tr class="c-table__tr">
                            <th class="c-table__th --w20pct --leftTop --noborder">{{ trans('messages.customer_name') }}<span class="require-txt">*</span>
                            </th>
                            <td class="c-table__td --rightTop --noborder">
                                <input class="c-input --max" type="text" name="customer_name"
                                    value="{{ old('customer_name', $customer['customer_name'] ?? '') }}" autocomplete="off">
                                @error('customer_name')
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15" data-error-name="customer_name">
                                        {{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                        <tr class="c-table__tr">
                            <th class="c-table__th --w20pct --noborder">{{ trans('messages.customer_desc') }}<span class="require-txt">*</span></th>
                            <td class="c-table__td --noborder">
                                <textarea class="c-textarea --max" name="description" autocomplete="off">{{ old('description', $customer['description'] ?? '') }}</textarea>
                                @error('description')
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 s-lh" data-error-name="description">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </td>
                        </tr>
                        <tr class="c-table__tr">
                            <th class="c-table__th --w20pct --noborder">{{ trans('messages.status') }}</th>
                            <td class="c-table__td --noborder">
                                <label class="switch">
                                    <input type="hidden" name="status" value="0" autocomplete="off">
                                    <input type="checkbox" name="status" value="1"
                                        {{ old('status', optional($customer)->status) == 1 ? 'checked' : '' }} autocomplete="off">
                                    <span class="switch-card round"></span>
                                </label>
                                @error('status')
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15" data-error-name="status">
                                        {{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="u-align--right">
                    <a class="c-btn --frame --bl u-mr20" href="{{ route('admin.customer.index') }}">{{ trans('messages.cancel') }}</a>
                    <button class="c-btn --bl js-form-submit" type="submit"
                        id="js-btn-submit">{{ empty($customer['id']) ? trans('messages.save') : trans('messages.edit') }}</button>
                </div>
            </form>
        </section>
    </div>
@endsection
