@extends('admin.layouts.app')
@section('content')
    <div class="l-inner">
        <ul class="c-breadcrumb">
            {{-- TODO:: add home button link --}}
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="#">{{ trans('messages.top') }}</a></li>
            <li class="c-breadcrumb__item"><span class="c-breadcrumb__link">{{ trans('generalConst.INQUIRY_MANAGEMENT') }}</span></li>
        </ul>
        <h1 class="u-heading-h1 u-mb20">{{ trans('generalConst.INQUIRY_MANAGEMENT') }}</h1>

        <form class="MKINQUIRY" action="{{ route('admin.inquiry.index') }}" method="GET">
            <div class="c-box --frame --search u-mb40 project-search-frame">
                <div class="c-box__input project-search u-mb15">
                    <div>
                        <label class="c-label --w100" for="project_name">{{ trans('messages.project_name')}} : </label>
                        <input class="c-input --w170 u-mr30" type="text" name="project_name"
                            value="{{ old('project_name', $search_info['project_name'] ?? '') }}" autocomplete="off">
                        @error('project_name')
                            <p class="u-color --warning u-fs12 u-fw--bold u-mt5 project-search-item" data-error-name="project_name">
                                {{ $message }}</p>
                        @enderror
                    </div>
                    <div class="project-list-datepicker u-mr5">
                        <label class="c-label --w105" for="response_date">{{ trans('messages.desired_date') }} : </label>
                        <label class="c-datepicker">
                            <input class="c-input --w170 u-mr30 datepicker" type="text" name="response_date"
                                value="{{ old('response_date', $search_info['response_date'] ?? '') }}" autocomplete="off">
                        </label>
                        @error('response_date')
                            <p class="u-color --warning u-fs12 u-fw--bold u-mt5 project-search-item" data-error-name="response_date">
                                {{ $message }}</p>
                        @enderror
                    </div>
                    <div class="--search">
                        <div class="project-search c-select --w270 u-mr30">
                            <label class="c-label --w175 --align" for="inquiry_status">{{ trans('messages.status') }} : </label>
                            <select class="c-select__item" name="inquiry_status" value="">
                                <option class="js-option-default" selected value="">
                                </option>
                                @foreach (GeneralConst::INQUIRY_STATUS as $key => $value)
                                    <option class="js-option-default" value="{{ $key }}"
                                        @if (old('inquiry_status', $search_info['inquiry_status'] ?? '') == (string) $key) selected @endif>{{ trans('generalConst.inquiry_status.' . $key) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('inquiry_status')
                            <p class="u-color --warning u-fs12 u-fw--bold u-mt5 project-search-item" data-error-name="inquiry_status">
                                {{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="c-box__btns u-mt15">
                    <a class="c-btn --frame --search u-mr20" href="{{ route('admin.inquiry.index') }}">{{ trans('messages.clear')}}</a>
                    <button class="c-btn --search" type="submit">{{ trans('messages.search')}}</button>
                </div>
            </div>
        </form>
        <section class="p-MK02013 MKINQUIRY">
            <div class="c-clm--between u-pb35 @if ($errors->any()) create-btn-position @endif">
                @if (!$errors->any())
                    <p class="total-count">{{ trans('messages.search_result') }}：<span class="u-fw--bold">{{ count($inquiry_list) }}</span> {{ trans('messages.item') }}</p>
                @endif
                <div class="u-align--right">
                    <a class="c-btn --bl --variable u-ml40 u-link --add2 project-add"
                        href="{{ route('admin.inquiry.save') }}">{{ trans('messages.new_create') }}</a>
                </div>
            </div>
            @if (!$errors->any())
                <table class="c-table">
                    <thead class="c-table-head">
                        <tr class="c-table__tr">
                            <th class="c-table__th p-MK02013-table__th --thead --w150">{{ trans('messages.inquiry_no') }}</th>
                            <th class="c-table__th p-MK02013-table__th --thead --w140">{{ trans('messages.project_name') }}</th>
                            <th class="c-table__th p-MK02013-table__th --thead --w140">{{ trans('messages.desired_date') }}</th>
                            <th class="c-table__th p-MK02013-table__th --thead --w120">{{ trans('messages.person_in_charge') }}</th>
                            <th class="c-table__th p-MK02013-table__th --thead --w100">{{ trans('messages.author') }}</th>
                            <th class="c-table__th p-MK02013-table__th --thead --w100">{{ trans('messages.priority') }}</th>
                            <th class="c-table__th p-MK02013-table__th --thead --w120">{{ trans('messages.status') }}</th>
                            <th class="c-table__th p-MK02013-table__th --thead --w12pct --rightTop">&nbsp;</th>
                        </tr>
                    </thead>
                    @php
                        $datetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Carbon\Carbon::now())->setTimezone($admin_login_session_data['time_zone'])->format('Y-m-d');
                    @endphp
                    <tbody class="c-table-body">
                        @if (count($inquiry_list) > 0)
                            @foreach ($inquiry_list as $inquiry)
                                <tr class="c-table__tr">
                                    <td class="c-table__td p-MK02013-table__td --w150">
                                        {{ 'QA_' . str_pad($inquiry->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td class="c-table__td p-MK02013-table__td">{{ $inquiry->project_name }}</td>
                                    <td class="c-table__td p-MK02013-table__td">
                                        <div class="icon-align @if (($inquiry->expected_answer_date <= $datetime) && ($inquiry->status != GeneralConst::CLOSE)) deadline @endif">
                                            {{ date('Y/m/d', strtotime($inquiry->expected_answer_date)) }} @if (($inquiry->expected_answer_date <= $datetime) && ($inquiry->status != GeneralConst::CLOSE))
                                            <img class="icon" src="{{ asset('img/admin/common/ico_hot.svg') }}"
                                                alt="期限アイコン">
                                            @endif
                                        </div>
                                    </td>
                                    <td class="c-table__td p-MK02013-table__td">{{ $inquiry->name }}</td>
                                    <td class="c-table__td p-MK02013-table__td">{{ $inquiry->created_user_name }} </td>
                                    <td class="c-table__td p-MK02013-table__td --w100">
                                        <div class="tooltip">
                                            <img class="priority-icon"
                                                src="{{ asset('img/admin/common/' . GeneralConst::PRIORITY[$inquiry->priority] . '.svg') }}"
                                                alt="{{ GeneralConst::PRIORITY[$inquiry->priority] }}アイコン">
                                            <span
                                                class="tooltiptext">{{ trans('generalConst.priority.' . $inquiry->priority) }}</span>
                                        </div>
                                    </td>
                                    <td class="c-table__td p-MK02013-table__td">
                                        {{ trans('generalConst.inquiry_status.' . $inquiry->status) }}</td>
                                    <td class="c-table__td p-MK02013-table__td --rightBottom">
                                        <a class="u-link list-detail" href="{{route('admin.inquiry.detail', ['inquiry_id' => $inquiry->id])}}">{{ trans('messages.detail') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="c-table__td p-MK02013-table__td u-align--center">{{ trans('messages.no_result') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            @endif
        </section>
    </div>
@endsection
