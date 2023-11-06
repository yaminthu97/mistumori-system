@extends('admin.layouts.app')
@section('content')
    <div class="l-inner">
        <ul class="c-breadcrumb">
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="{{ route('admin.account.index') }}">{{ trans('messages.top') }}</a></li>
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="{{ route('admin.project.index') }}">{{ trans('generalConst.PROJECT_MANAGEMENT') }}</a></li>
            <li class="c-breadcrumb__item"><span class="c-breadcrumb__link">{{ trans('messages.project_detail') }}</span></li>
        </ul>
        <div class="c-invite-clm">
            <div class="c-invite-main">
                <h2 class="u-heading-h1 u-mb20">{{ trans('messages.project_detail')}}</h2>
            </div>
        </div>
        <section class="p-MK02016 u-mb40 pj-detail-screen">
            <form action="" method="GET">
                    <h3 class="u-heading-h3 c-section-header__title u-mb20">{{ trans('messages.project_info') }}</h3>
                    <div class="btn-row">
                        <a href="{{ route('admin.project.index') }}" class="c-btn --frame --bl u-mr20 back-btn">{{ trans('messages.back') }}</a>
                        <a href="{{ route('admin.project.save', $project->id) }}" class="c-btn --bl"
                            id="js-btn-submit">{{ trans('messages.edit') }}</a>
                    </div>
                    <table class="c-table pj-detail-table">
                        <input type="hidden" id="project_data" name="project_data" value="" />
                        <tbody class="c-table-body --horizontal">
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.project_name') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <input class="c-input u-mr20" name="project_name"
                                        value="{{ isset($project->project_name) ? $project->project_name : '' }}" readonly>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.customer_name') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <input class="c-input u-mr20" name="customer_name"
                                        value="{{ isset($project->customer_name) ? $project->customer_name : '' }}"
                                        readonly>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.project_type') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <input class="c-input u-mr20" name="project_type"
                                        value="{{ isset($project->project_type) ? trans('generalConst.project_type.' .$project->project_type) : '' }}"
                                        readonly>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.system_content') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <p class="txt-overview u-mr20">{!! nl2br(str_replace(' ', '&nbsp;', $project->system_overview)) !!}</p>
                                    <a href="{{ route('admin.systemContent.download', ['project_id' => $project->id, 'filepath' => basename($project->system_overview_file_path)]) }}"
                                        class="a-link js-system_overview-download">{{ basename($project->system_overview_file_path) }}</a>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.dev_process') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <input class="c-input u-mr20" name="phases"
                                        value="{{ isset($project->phases) ? $project->phases : '' }}" readonly>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.dev_language') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <input class="c-input u-mr20" name="language"
                                        value="{{ isset($project->language) ? $project->language : '' }}" readonly>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.server_env') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <input class="c-input u-mr20" name="server_env"
                                        value="{{ isset($project->server_env) ? $project->server_env : '' }}" readonly>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.dev_period') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <input class="c-input u-mr20" name="expected_date"
                                        @if (isset($project->expected_dev_start_date) && isset($project->expected_dev_end_date)) value="{{ date('Y/m/d', strtotime($project->expected_dev_start_date)) }} ~ {{ date('Y/m/d', strtotime($project->expected_dev_end_date)) }}" @endif
                                        readonly>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.estimated_date') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <input class="c-input u-mr20" name="expected_submit_date"
                                        value="{{ isset($project->expected_submit_date) ? date('Y/m/d', strtotime($project->expected_submit_date)) : '' }}"
                                        readonly>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.priority') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <input class="c-input u-mr20" name="priority"
                                        value="{{ isset($project->priority) ?  trans('generalConst.priority.' . $project->priority) : '' }}"
                                        readonly>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.status') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <p class="c-input u-mr20" id="status_detail" readonly>
                                        {{ trans('generalConst.project_status.' . $project->status) }}</p>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.person_in_charge') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <p class="c-input u-mr20" id="assignee_name_detail" readonly>
                                        {{ $project->assignee_name }}</p>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.author') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <input class="c-input u-mr20" name="creator_name"
                                        value="{{ isset($project->creator_name) ? $project->creator_name : '' }}"
                                        readonly>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop">{{ trans('messages.inquiry_quantity') }}</th>
                                <td class="c-table__td --form --rightTop">
                                    <a href="{{ route('admin.inquiry.index', ['project_name' => $project->project_name]) }}"
                                        class="c-input a-link">{{ $question_count > 0 ? $question_count : '' }} {{ trans('messages.item') }}</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </form>
            @include('admin.comment.index')
        </section>
    </div>
@endsection

@section('script')
    @parent
    <script src="{{ asset('js/admin/tab.js' . jsCssForceReload()) }}" defer></script>
    <script src="{{ asset('js/admin/comment.js' . jsCssForceReload()) }}" defer></script>
    <script src="{{ asset('js/admin/estimate.js' . jsCssForceReload()) }}" defer></script>
    <script>
        const PROJECT_STATUS = @json(GeneralConst::PROJECT_STATUS, JSON_PRETTY_PRINT);
    </script>
@endsection
