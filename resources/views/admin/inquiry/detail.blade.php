@extends('admin.layouts.app')
@section('content')
    @php
        $editable = $new_created_inquiry->created_user_id == Auth::user()->id;
        $not_started = $new_created_inquiry->status == GeneralConst::NOT_STARTED;
        $inquiry_not_started = GeneralConst::NOT_STARTED;
    @endphp
    <div class="l-inner">
        <ul class="c-breadcrumb">
            {{-- TODO:: add home button link --}}
            <li class="c-breadcrumb__item"><a href="#" class="c-breadcrumb__link">{{ trans('messages.top')}}</a></li>
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="{{ route('admin.inquiry.index') }}">{{ trans('generalConst.INQUIRY_MANAGEMENT') }}</a></li>
            <li class="c-breadcrumb__item"><span class="c-breadcrumb__link">{{ trans('messages.inquiry_detail') }}</span></li>
        </ul>
        <div class="c-invite-clm">
            <div class="c-invite-main">
                <h2 class="u-heading-h1 u-mb20">{{ trans('messages.inquiry_detail') }}</h2>
            </div>
        </div>
        <div class="MKINQUIRY">
            <section class="p-MK02016 u-mb40 pj-detail-screen">
                <h3 class="u-heading-h3 c-section-header__title u-mb20">{{ trans('messages.inquiry_detail') }}</h3>
                <div class="btn-row">
                    <a href="{{ route('admin.inquiry.index') }}" class="c-btn --frame --bl u-mr20 back-btn">{{ trans('messages.back') }}</a>
                    <a href="{{ route('admin.inquiry.save', ['inquiry_id' => $new_created_inquiry->id]) }}"
                        class="c-btn --bl @if (!$editable || !$not_started) edit-link-disable @endif"
                        id="js-btn-submit">{{ trans('messages.edit') }}</a>
                </div>
                <table class="c-table">
                    <tbody class="c-table-body --horizontal">
                        <tr class="c-table__tr">
                            <th class="c-table__th --w200 --leftTop">{{ trans('messages.inquiry_no') }}</th>
                            <td class="c-table__td --form --rightTop">
                                <p class="c-input u-mr20">
                                    {{ 'QA_' . str_pad($new_created_inquiry->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </td>
                        </tr>
                        <tr class="c-table__tr">
                            <th class="c-table__th --w200 --leftTop">{{ trans('messages.project_name') }}</th>
                            <td class="c-table__td --form --rightTop">
                                <p class="c-input u-mr20">{{ $new_created_inquiry->project_name }}</p>
                            </td>
                        </tr>
                        <tr class="c-table__tr">
                            <th class="c-table__th --w200 --leftTop">{{ trans('messages.inquiry_content') }}</th>
                            <td class="c-table__td --form --rightTop">
                                <p class="txt-overview">{!! nl2br(str_replace(' ', '&nbsp;', $new_created_inquiry->comment_content)) !!}</p>
                            </td>
                        </tr>
                        <tr class="c-table__tr">
                            <th class="c-table__th --w200 --leftTop">{{ trans('messages.inquirer') }}</th>
                            <td class="c-table__td --form --rightTop">
                                <input class="c-input u-mr20" name="created_user_id"
                                    value="{{ $new_created_inquiry->created_user_name }}" readonly>
                            </td>
                        </tr>
                        <tr class="c-table__tr">
                            <th class="c-table__th --w200 --leftTop">{{ trans('messages.person_in_charge') }}</th>
                            <td class="c-table__td --form --rightTop">
                                <p class="c-input u-mr20" id="question_assignee_name_detail">
                                    {{ $new_created_inquiry->question_assignee_name }}</p>
                            </td>
                        </tr>
                        <tr class="c-table__tr">
                            <th class="c-table__th --w200 --leftTop">{{ trans('messages.desired_date') }}</th>
                            <td class="c-table__td --form --rightTop">
                                <p class="c-input u-mr20">
                                    {{ date('Y/m/d', strtotime($new_created_inquiry->expected_answer_date)) }}</p>
                            </td>
                        </tr>
                        <tr class="c-table__tr">
                            <th class="c-table__th --w200 --leftTop">{{ trans('messages.priority') }}</th>
                            <td class="c-table__td --form --rightTop">
                                <p class="c-input --fw500 u-mr20">
                                    {{ trans('generalConst.priority.' . $new_created_inquiry->priority) }}
                                </p>
                            </td>
                        </tr>
                        <tr class="c-table__tr">
                            <th class="c-table__th --w200 --leftTop">{{ trans('messages.status') }}</th>
                            <td class="c-table__td --form --rightTop">
                                <p class="c-input --fw500 u-mr20" id="inquiry_status">
                                    {{ trans('generalConst.inquiry_status.' . $new_created_inquiry->status) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                @include('admin.inquiry.answer')
            </section>
        </div>
    </div>
@endsection

@section('script')
    @parent
    <script>
        const INQUIRY_NOT_STARTED = @json($inquiry_not_started, JSON_PRETTY_PRINT);
    </script>
    <script src="{{ asset('js/admin/answer.js' . jsCssForceReload()) }}" defer></script>
@endsection
