@extends('admin.layouts.app')
@section('content')
    <div class="l-inner">
        <ul class="c-breadcrumb">
            {{-- // TODO:: To add top page route --}}
            <div class="c-breadcrumb-onboard">
                <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="">{{ trans('messages.top') }}</a></li>
                <li class="c-breadcrumb__item"><span class="c-breadcrumb__link">{{ trans('generalConst.PROJECT_MANAGEMENT') }}</span></li>
            </div>
        </ul>
        <h1 class="u-heading-h1 u-mb20">{{ trans('generalConst.PROJECT_MANAGEMENT') }}</h1>

        <div class="MKPROJECT">
            <form action="{{ route('admin.project.index') }}" method="GET">
                <div class="c-box --frame --search u-mb40 project-search-frame">
                    <div class="c-box__input project-search u-mb15">
                        <div class="">
                            <label class="c-label --w100" for="project_name">{{ trans('messages.project_name') }} : </label>
                            <input class="c-input --w170 u-mr30" type="text" name="project_name"
                                value="{{ old('project_name', $search_info['project_name'] ?? '') }}" autocomplete="off">
                            @error('project_name')
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 project-search-item"
                                    data-error-name="project_name">
                                    {{ $message }}</p>
                            @enderror
                        </div>
                        <div class="">
                            <div class="customer-box project-search c-select --w310">
                                <label class="c-label --w200" for="customer_name">{{ trans('messages.customer_name') }} : </label>
                                <select class="c-select__item u-mr35" name="customer_name" value=""
                                    autocomplete="off">
                                    <option class="js-option-default" selected value="">
                                    </option>
                                    @foreach ($customers as $key => $customer)
                                        <option class="js-option-default" value="{{ $customer->customer_name }}"
                                            @if (old('customer_name', $search_info['customer_name'] ?? '') == (string) $customer->customer_name) selected @endif>{{ $customer->customer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('customer_name')
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 project-search-item"
                                    data-error-name="customer_name">
                                    {{ $message }}</p>
                            @enderror
                        </div>
                        <div class="">
                            <div class="assignee-box">
                                <div class="assignee-box project-search c-select --w240">
                                    <label class="c-label" for="assignee">{{ trans('messages.person_in_charge') }} : </label>
                                    <select class="c-select__item u-mr10 select-assignee" name="assignee" value=""
                                        autocomplete="off">
                                        <option class="js-option-default" selected value="">
                                        </option>
                                        @foreach ($users as $key => $user)
                                            <option class="js-option-default" value="{{ $user->name }}"
                                                @if (old('assignee', $search_info['assignee'] ?? '') == (string) $user->name) selected @endif>{{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" id="login-user" value="{{ Auth::user()->name }}">
                                <img class="assignee-icon u-mr25" src="{{ asset('img/admin/assignee-icon.svg') }}"
                                    alt="" />
                            </div>
                            @error('assignee')
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 project-search-item" data-error-name="assignee">
                                    {{ $message }}</p>
                            @enderror
                        </div>
                        <div class="icon-btn @if (!empty($search_info['priority']) || isset($search_info['status']) || !empty($search_info['submit_date']) || $errors->has('status')) open @endif"></div>
                    </div>

                    <div
                        class="c-box__input project-search u-mt10 u-mb15 updown-box u-mr13 @if (!empty($search_info['priority']) || isset($search_info['status']) || !empty($search_info['submit_date']) || old('submit_date') || $errors->has('status')) open @endif">
                        <div class="--search">
                            <div class="project-search c-select --w270 u-mr30">
                                <label class="c-label --w175 --align" for="status">{{ trans('messages.status') }} : </label>
                                <select class="c-select__item" name="status" value="" autocomplete="off">
                                    <option class="js-option-default" selected value="">
                                    </option>
                                    @foreach (GeneralConst::PROJECT_STATUS as $key => $value)
                                        <option class="js-option-default" value="{{ $key }}"
                                            @if (old('status', $search_info['status'] ?? '') == (string) $key) selected @endif>{{ trans('generalConst.project_status.' . $key) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('status')
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 project-search-item" data-error-name="status">
                                    {{ $message }}</p>
                            @enderror
                        </div>
                        <div class="project-list-datepicker u-mr5 --w306">
                            <label class="c-label --w105" for="submit_date">{{ trans('messages.estimated_date') }} : </label>
                            <label class="c-datepicker">
                                <input class="c-input --w170 u-mr30 datepicker" type="text" name="submit_date"
                                    value="{{ old('submit_date', $search_info['submit_date'] ?? '') }}" autocomplete="off">
                            </label>
                            @error('submit_date')
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5" data-error-name="submit_date">
                                    {{ $message }}</p>
                            @enderror
                        </div>
                        <div class="--search u-mt10">
                            <div class="priority-box">
                                <label class="c-label" for="priority">{{ trans('messages.priority') }} : </label>
                                @foreach (GeneralConst::PRIORITY as $key => $value)
                                    <label class="c-checkbox">
                                        <input class="c-checkbox__input" type="checkbox" name="priority[]"
                                            value="{{ $key }}" @if (in_array($key, old('priority', $search_info['priority'] ?? []))) checked @endif>
                                        <span class="c-checkbox__label u-mr10"></span>
                                    </label>
                                    <span class="u-mr10">{{ trans('generalConst.priority.' . $key) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="c-box__btns u-mt15">
                        <a class="c-btn --frame --search u-mr20" href="{{ route('admin.project.index') }}">{{ trans('messages.clear') }}</a>
                        <button class="c-btn --search" type="submit">{{ trans('messages.search') }}</button>
                    </div>
                </div>
            </form>
            <section class="p-MK02013">
                <div class="c-clm--between u-pb35 @if ($errors->any()) create-btn-position @endif">
                    @if (!$errors->any())
                        <p class="total-count">{{ trans('messages.search_result') }}：<span class="u-fw--bold">{{ count($projects) }}</span> {{ trans('messages.item') }}</p>
                    @endif
                    <div class="u-align--right">
                        <a class="c-btn --bl --variable u-ml40 u-link --add2 project-add"
                            href="{{ route('admin.project.save') }}">{{ trans('messages.new_create') }}</a>
                    </div>
                </div>

                @if (!$errors->any())
                    <table class="c-table">
                        <thead class="c-table-head">
                            <tr class="c-table__tr">
                                <th class="c-table__th p-MK02013-table__th --thead --w140">{{ trans('messages.project_name') }}</th>
                                <th class="c-table__th p-MK02013-table__th --thead">{{ trans('messages.customer_name') }}</th>
                                <th class="c-table__th p-MK02013-table__th --thead --w140">{{ trans('messages.estimated_date') }}</th>
                                <th class="c-table__th p-MK02013-table__th --thead">{{ trans('messages.person_in_charge') }}</th>
                                <th class="c-table__th p-MK02013-table__th --thead --w100">{{ trans('messages.author') }}</th>
                                <th class="c-table__th p-MK02013-table__th --thead --w100">{{ trans('messages.priority') }}</th>
                                <th class="c-table__th p-MK02013-table__th --thead">{{ trans('messages.status') }}</th>
                                <th class="c-table__th p-MK02013-table__th --thead --w12pct --rightTop">&nbsp;</th>
                            </tr>
                        </thead>

                        @php
                            $datetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Carbon\Carbon::now())->setTimezone($admin_login_session_data['time_zone'])->format('Y-m-d');
                        @endphp
                        <tbody class="c-table-body">
                            @if (empty(count($projects)))
                                <tr>
                                    <td class="c-table__td p-MK02013-table__td u-align--center" colspan="8">
                                        {{ trans('messages.no_result') }}
                                    </td>
                                </tr>
                            @else
                                @foreach ($projects as $project)
                                    <tr class="c-table__tr">
                                        <td class="c-table__td p-MK02013-table__td --w140">{{ $project->project_name }}
                                        </td>
                                        <td class="c-table__td p-MK02013-table__td">{{ $project->customer_name }}</td>
                                        <td
                                            class="c-table__td p-MK02013-table__td --w140">
                                            <div class="icon-align @if (($project->expected_submit_date <= $datetime) && ($project->status != GeneralConst::COMPLETED)) deadline @endif">
                                                {{ date('Y/m/d', strtotime($project->expected_submit_date)) }} @if (($project->expected_submit_date <= $datetime) && ($project->status != GeneralConst::COMPLETED))
                                                <img class="icon" src="{{ asset('img/admin/common/ico_hot.svg') }}"
                                                    alt="期限アイコン">
                                                @endif
                                            </div>
                                        </td>
                                        <td class="c-table__td p-MK02013-table__td">{{ $project->assignee }}</td>
                                        <td class="c-table__td p-MK02013-table__td">{{ $project->created_user_name }}</td>
                                        <td class="c-table__td p-MK02013-table__td --w80">
                                            <div class="tooltip">
                                                <img class="priority-icon"
                                                    src="{{ asset('img/admin/common/' . GeneralConst::PRIORITY[$project->priority] . '.svg') }}"
                                                    alt="{{ GeneralConst::PRIORITY[$project->priority] }}アイコン">
                                                <span
                                                    class="tooltiptext"> {{ trans('generalConst.priority.' . $project->priority) }}</span>
                                            </div>
                                        </td>
                                        <td class="c-table__td p-MK02013-table__td">
                                            {{ trans('generalConst.project_status.' . $project->status) }}
                                        </td>
                                        <td class="c-table__td p-MK02013-table__td --rightBottom">
                                            <a class="u-link --link-color"
                                                href="{{ route('admin.project.detail', [$project->id]) }}">{{ trans('messages.detail') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                @endif
            </section>
        </div>
    </div>
@endsection

@section('script')
    @parent
    <script>
        const onboarded = @json($onboarded, JSON_PRETTY_PRINT);
        const UNDONE_ONBOARD = @json(GeneralConst::UNDONE_ONBOARD, JSON_PRETTY_PRINT);
        const userId = @json(Auth::user()->id, JSON_PRETTY_PRINT);
    </script>
    <script src="{{ asset('js/admin/project.js' . jsCssForceReload()) }}" defer></script>
@endsection
