@extends('admin.layouts.app')
@section('content')
    <div class="l-inner">
        <ul class="c-breadcrumb">
            {{-- // TODO:: To add top page route --}}
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="">{{ trans('messages.top') }}</a></li>
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="{{ route('admin.project.index') }}">{{ trans('generalConst.PROJECT_MANAGEMENT') }}</a></li>
            <li class="c-breadcrumb__item">
                <span class="c-breadcrumb__link">
                    {{ empty($project['id']) ? trans('messages.project_add') : trans('messages.project_edit') }}
                </span>
            </li>
        </ul>
        <h1 class="u-heading-h1 u-mb20">
            {{ empty($project['id']) ? trans('messages.project_add') : trans('messages.project_edit') }}
        </h1>

        <div class="MKPROJECT">
            <section class="p-MK02013 u-mb40">
                <div class="c-section-header__title c-clm--between u-mb20">
                    <h2 class="u-heading-h3">{{ trans('messages.project_info') }}</h2>
                </div>
                <form action="{{ route('admin.project.save', $project['id'] ?? null) }}" id="projectForm" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="status" value="{{ $project['status'] ?? GeneralConst::NOT_STARTED }}" />
                    <div class="project-wrapper">
                        <div class="project-name">
                            <label class="c-label --align project-input-label" for="">{{ trans('messages.project_name') }}<span class="error">*</span></label>
                            <div class="project-input-wrapper">
                                <label class="project-content">
                                    <input class="c-input project-content" type="text" name="project_name"
                                        value="{{ old('project_name', $project['project_name'] ?? '') }}" autocomplete="off">
                                </label>
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="project_name_error">
                            </div>
                        </div>
                        <label class="c-select project-name">
                            <label class="c-label --align project-input-label" for="customer_id">{{ trans('messages.customer_name') }}<span class="error">*</span></label>
                            <div class="project-input-wrapper">
                                <select class="c-select__item js-select" name="customer_id" autocomplete="off">
                                    <option selected></option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" @if (old('customer_id', $project['customer_id'] ?? '') == (string) $customer->id) selected @endif>
                                            {{ $customer->customer_name }}</option>
                                    @endforeach
                                </select>
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="customer_id_error">
                            </div>
                        </label>
                        <label class="c-select project-name">
                            <label class="c-label --align project-input-label" for="">{{ trans('messages.project_type') }}<span class="error">*</span></label>
                            <div class="project-input-wrapper">
                                <select class="c-select__item js-select" name="project_type" autocomplete="off">
                                    <option selected></option>
                                    @foreach (GeneralConst::PROJECT_TYPE as $key => $value)
                                        <option value="{{ $key }}" @if (old('project_type', $project['project_type'] ?? '') == (string) $key) selected @endif>
                                            {{ trans('generalConst.project_type.' . $key) }}</option>
                                    @endforeach
                                </select>
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="project_type_error">
                            </div>
                        </label>
                        <div class="project-name">
                            <label class="c-label project-input-label --p47" for="">{{ trans('messages.system_content') }}<span class="error">*</span></label>
                            <div class="project-input-wrapper">
                                <textarea class="c-textarea --h100 project-content" name="system_content" autocomplete="off">{{ old('system_content', $project['system_overview'] ?? '') }}</textarea>
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="system_content_error">
                            </div>
                        </div>
                        <div class="project-name u-mb20">
                            <label class="c-label --align project-input-label" for=""></label>
                            <div class="project-input-wrapper">
                                <div class="file-wrapper">
                                    <label class="file file-content --frame --file-box">
                                        <input class="c-input --file-input" type="text" name="system_file"
                                            value="{{ old('system_file', isset($project['system_overview_file_path']) ? basename($project['system_overview_file_path']) : '') }}"
                                            placeholder="Choose file" autocomplete="off" readonly>
                                        <input type="file" class="js_input" id="system_path" name="system_path"
                                            value="" hidden>
                                        <label class="c-btn --gr --file-btn" for="system_path">Browse</label>
                                    </label>
                                    <button class="c-btn --gr clear-btn" type="button">Clear</button>
                                </div>
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="system_path_error">
                            </div>
                        </div>
                        <div class="project-name">
                            <label class="c-label --align project-input-label" for="">{{ trans('messages.dev_process') }}<span class="error">*</span></label>
                            <label class="project-content">
                                <div class="project-input-wrapper">
                                    <input class="c-input project-content" type="text" name="development_process"
                                        value="{{ old('development_process', $project['phases'] ?? '') }}" autocomplete="off">
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="development_process_error">
                                </div>
                            </label>
                        </div>
                        <div class="project-name">
                            <label class="c-label --align project-input-label" for="">{{ trans('messages.dev_language') }}<span class="error">*</span></label>
                            <label class="project-content">
                                <div class="project-input-wrapper">
                                    <input class="c-input project-content" type="text" name="development_language"
                                        value="{{ old('development_language', $project['language'] ?? '') }}"
                                        autocomplete="off">
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="development_language_error">
                                </div>
                            </label>
                        </div>
                        <div class="project-name">
                            <label class="c-label --align project-input-label" for="">{{ trans('messages.server_env') }}</label>
                            <label class="project-content">
                                <div class="project-input-wrapper">
                                    <input class="c-input project-content" type="text" name="server_environment"
                                        value="{{ old('server_environment', $project['server_env'] ?? '') }}"
                                        autocomplete="off">
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="server_environment_error">
                                </div>
                            </label>
                        </div>
                        <div class="project-name">
                            <label class="c-label --align project-input-label" for="">{{ trans('messages.dev_period') }}</label>
                            <div class="project-input-wrapper date-content">
                                <label class="c-datepicker">
                                    <input class="c-input project-content datepicker" type="text"
                                        name="development_start_date"
                                        value="{{ old('development_start_date', isset($project['expected_dev_start_date']) ? date('Y/m/d', strtotime($project['expected_dev_start_date'])) : '') }}"
                                        autocomplete="off">
                                </label>
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="development_start_date_error">
                            </div>
                            <span class="mark"> ~ </span>
                            <div class="project-input-wrapper date-content">
                                <label class="c-datepicker">
                                    <input class="c-input project-content datepicker" type="text"
                                        name="development_end_date"
                                        value="{{ old('development_end_date', isset($project['expected_dev_end_date']) ? date('Y/m/d', strtotime($project['expected_dev_end_date'])) : '') }}"
                                        autocomplete="off">
                                </label>
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="development_end_date_error">
                            </div>
                        </div>
                        <div class="project-name">
                            <label class="c-label --align project-input-label" for="">{{ trans('messages.estimated_date') }}<span class="error">*</span></label>
                            <div class="project-input-wrapper">
                                <label class="c-datepicker project-content">
                                    <input class="c-input project-content datepicker" type="text" name="submit_date"
                                        value="{{ old('submit_date', isset($project['expected_submit_date']) ? date('Y/m/d', strtotime($project['expected_submit_date'])) : '') }}"
                                        autocomplete="off">
                                </label>
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="submit_date_error">
                            </div>
                        </div>
                        <label class="c-select project-name">
                            <label class="c-label --align project-input-label" for="">{{ trans('messages.priority') }}<span class="error">*</span></label>
                            <div class="project-input-wrapper">
                                <select class="c-select__item js-select" name="priority" autocomplete="off">
                                    <option selected></option>
                                    @foreach (GeneralConst::PRIORITY as $key => $value)
                                        <option value="{{ $key }}" @if (old('priority', $project['priority'] ?? '') == (string) $key) selected @endif>
                                            {{ trans('generalConst.priority.' . $key) }}</option>
                                    @endforeach
                                </select>
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="priority_error">
                            </div>
                        </label>
                        <label class="c-select project-name">
                            <label class="c-label --align project-input-label" for="">{{ trans('messages.person_in_charge') }}<span class="error">*</span></label>
                            <div class="project-input-wrapper">
                                <select class="c-select__item js-select" name="assignee" autocomplete="off">
                                    <option selected></option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @if (old('assignee', $project['assignee'] ?? '') == (string) $user->id) selected @endif>
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15 invalid-error" id="assignee_error">
                            </div>
                        </label>
                        <div class="c-box__btns u-align--right u-mt10">
                            <a class="c-btn --frame --bl u-mr20"
                                href="{{ empty($project['id']) ? route('admin.project.index') : route('admin.project.detail', [$project['id']]) }}">{{ trans('messages.cancel') }}</a>
                            <button class="c-btn --bl" type="submit">{{ empty($project['id']) ? trans('messages.save') : trans('messages.edit') }}</button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection

@section('script')
    @parent
    <script src="{{ asset('js/admin/project.js' . jsCssForceReload()) }}" defer></script>
@endsection
