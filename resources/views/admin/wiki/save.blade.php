@extends('admin.layouts.app')
@section('content')
    <div class="MKWIKI l-inner">
        <ul class="c-breadcrumb">
            {{-- // TODO:: To add top page route --}}
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="">{{ trans('messages.top') }}</a></li>
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="{{ route('admin.wiki.index') }}">{{ trans('generalConst.WIKI') }}</a></li>
            <li class="c-breadcrumb__item">
                <span class="c-breadcrumb__link">
                    {{ isset($wiki['id']) ? trans('messages.wiki_edit') : trans('messages.wiki_create') }}
                </span>
            </li>
        </ul>
        <section class="u-mb40 wiki">
            <div class="left-main-box">
                <div class="ttl-box">
                    <h1 class="u-heading-h1">
                        {{ trans('generalConst.WIKI') }}
                    </h1>
                </div>
                <form class="left-box u-mt20" action="{{ route('admin.wiki.save', $wiki['id'] ?? null) }}" id="wiki-form"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="c-box__btns u-align--right u-mt10">
                        <a class="c-btn --frame --bl back-btn u-mr20" href="{{ route('admin.wiki.index') }}">{{ trans('messages.back') }}</a>
                        <button class="c-btn --bl js-form-submit" type="submit">{{ isset($wiki['id']) ? trans('messages.edit') : trans('messages.add') }}</button>
                    </div>
                    <div class="wiki-box u-mt10 u-mb30">
                        <label class="c-label --align u-mb10">{{ trans('messages.page_name') }}<span class="u-color --warning">*</span></label>
                        <input class="c-input --max" type="text" id="wiki_title" name="wiki_title"
                            value="{{ old('wiki_title', $wiki['title'] ?? '') }}" autocomplete="off">
                        <p class="u-color --warning u-fs12 u-fw--bold u-mt5" id="wiki_title_error">
                    </div>
                    <div class="wiki-box u-mt10 u-mb30">
                        <label class="c-label --align u-mb10">{{ trans('messages.content') }}<span class="u-color --warning">*</span></label>
                        <input type="hidden" name="wiki_content" value="{{ old('wiki_content', $wiki['content'] ?? '') }}"
                            autocomplete="off">
                        <div class="--max text-editor" id="editor"></div>
                        <p class="u-color --warning u-fs12 u-fw--bold u-mt5" id="wiki_content_error">
                    </div>
                    <div class="wiki-box u-mt10 u-mb30">
                        <label class="c-label --align u-mb10">{{ trans('messages.attach_file') }}</label>
                        <div class="file-wrapper">
                            <label class="file-content --frame --file-box">
                                <input class="c-input --file-input" type="text" name="wiki_file"
                                    value="{{ old('wiki_file', isset($file_names) ? $file_names : '') }}"
                                    placeholder="Choose file" autocomplete="off" readonly>
                                <input type="file" class="js_input" id="wiki_path" name="wiki_path[]" value=""
                                    hidden multiple>
                                <label class="c-btn --gr --file-btn" for="wiki_path">Browse</label>
                            </label>
                            <button class="c-btn --gr clear-btn" type="button">Clear</button>
                        </div>
                        <p class="u-color --warning u-fs12 u-fw--bold u-mt5" id="wiki_path_error">
                    </div>
                </form>
            </div>

            {{-- // TODO:: To add right box code --}}
            <div class="right-main-box">
            </div>
        </section>
        @foreach ($latest_modifier_arr as $key => $value)
            <div class="modifier-box u-mb15">
                <img class="modifier-img" src="{{ asset('img/admin/common/icon_user.svg') }}" alt="Modifier Image">
                <p class="u-ml10">modified by {{ $key }} at {{ displayDateTimeByTimeZone($value) }}.</p>
            </div>
        @endforeach
    </div>
@endsection

@section('script')
    @parent
    <script src="{{ asset('js/admin/wiki.js' . jsCssForceReload()) }}" defer></script>
@endsection
