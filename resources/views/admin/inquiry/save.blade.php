@extends('admin.layouts.app')
@section('content')
    <div class="l-inner">
        <ul class="c-breadcrumb">
            {{-- TODO:: add home page link later --}}
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="">{{ trans('messages.top') }}</a></li>
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="{{ route('admin.inquiry.index') }}">{{ trans('generalConst.INQUIRY_MANAGEMENT') }}</a></li>
            <li class="c-breadcrumb__item"><span class="c-breadcrumb__link">{{ $created_inquiry ? trans('messages.inquiry_edit') : trans('messages.inquiry_add') }}</span></li>
        </ul>

        <h1 class="u-heading-h1 u-mb20">{{ $created_inquiry ? trans('messages.inquiry_edit') : trans('messages.inquiry_add') }}</h1>
        <div class="MKINQUIRY">
            <section class="p-MK02013">
                <div class="c-section-header__title c-clm--between u-mb30">
                    <h2 class="u-heading-h3">{{ $created_inquiry ? trans('messages.inquiry_edit') : trans('messages.inquiry_add') }}</h2>
                </div>
                <form action="{{ route('admin.inquiry.save', ['inquiry_id' => $created_inquiry->id ?? '']) }}"
                    method="POST">
                    @csrf
                    <input type="hidden" name="created_user_id" value="{{ $admin_login_session_data['id'] }}" />
                    <div class="inquiry-wrapper">
                        <label class="c-select inquiry-project-name">
                            <label class="c-label --align inquiry-input-label" for="project_name">{{ trans('messages.project_name') }}<sup style="color:red;">*</sup> </label>
                            <div class="inquiry-input-wrapper">
                                <select class="c-select__item js-select" name="project_name">
                                    <option selected></option>
                                    @foreach ($projects as $project)
                                        <option @if (old('project_name', $created_inquiry->project_id ?? '') == (string) $project->id) selected @endif
                                            value="{{ $project->id }}">
                                            {{ $project->project_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_name')
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15">
                                        {{ $message }}</p>
                                @enderror
                            </div>

                        </label>
                        <div class="inquiry-project-name">
                            <label class="c-label inquiry-input-label --p47" for="comment_content">{{ trans('messages.inquiry_content') }}<sup style="color:red;">*</sup></label>
                            <div class="inquiry-input-wrapper">
                                <textarea class="c-textarea --h100 inquiry-content" name="comment_content"> {{ old('comment_content', $created_inquiry->comment_content ?? '') }}</textarea>
                                @error('comment_content')
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15">
                                        {{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <label class="c-select inquiry-project-name">
                            <label class="c-label --align inquiry-input-label" for="question_assignee">{{ trans('messages.person_in_charge') }}<sup style="color:red;">*</sup></label>
                            <div class="inquiry-input-wrapper"><select class="c-select__item js-select"
                                    name="question_assignee">
                                    <option selected></option>
                                    @foreach ($users as $user)
                                        <option @if (old('question_assignee', $created_inquiry->question_assignee ?? '') == (string) $user->id) selected @endif
                                            value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('question_assignee')
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15">
                                        {{ $message }}</p>
                                @enderror
                            </div>
                        </label>
                        <div class="inquiry-project-name">
                            <label class="c-label --align inquiry-input-label" for="expected_answer_date">{{ trans('messages.desired_date') }}<sup style="color:red;">*</sup></label>
                            <div class="inquiry-input-wrapper"><label class="c-datepicker inquiry-content">
                                    <input class="c-input inquiry-content datepicker" type="text"
                                        name="expected_answer_date"
                                        value="{{ old('expected_answer_date', isset($created_inquiry->expected_answer_date) ? date('Y/m/d', strtotime($created_inquiry->expected_answer_date)) : '') }}"
                                        autocomplete="off">
                                </label>
                                @error('expected_answer_date')
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15">
                                        {{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <label class="c-select inquiry-project-name">
                            <label class="c-label --align inquiry-input-label" for="priority">{{ trans('messages.priority') }}<sup style="color:red;">*</sup></label>
                            <div class="inquiry-input-wrapper"><select class="c-select__item js-select" name="priority">
                                    <option selected></option>
                                    @foreach (GeneralConst::PRIORITY as $key => $value)
                                        <option @if (old('priority', $created_inquiry->priority ?? '') == (string) $key) selected @endif
                                            value="{{ $key }}">
                                            {{ trans('generalConst.priority.' . $key) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <p class="u-color --warning u-fs12 u-fw--bold u-mt5 u-mb15">
                                        {{ $message }}</p>
                                @enderror
                            </div>
                        </label>
                        <div class="inquiry-btns-wrapper">
                            <div class="c-box__btns inquiry-btns">
                                <a class="c-btn --bl --frame u-mr15" id="clear"
                                    href="{{ $created_inquiry ? route('admin.inquiry.detail', ['inquiry_id' => $created_inquiry->id]) : route('admin.inquiry.index') }}">{{ trans('messages.cancel') }}</a>
                                <button class="c-btn --bl js-form-submit" type="submit">{{ trans('messages.save') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection

@section('script')
    @parent
@endsection
