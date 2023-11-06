@extends('admin.layouts.app')
@section('content')
    <div class="MKWIKI l-inner">
        <ul class="c-breadcrumb">
            {{-- // TODO:: To add top page route --}}
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="">{{ trans('messages.top') }}</a></li>
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link"
                    href="{{ route('admin.wiki.index') }}">{{ trans('generalConst.WIKI') }}</a></li>
            <li class="c-breadcrumb__item">
                <span class="c-breadcrumb__link">
                    {{ trans('messages.wiki_detail') }}
                </span>
            </li>
        </ul>
        <section class="u-mb40 wiki">
            <div class="left-main-box">
                <div class="ttl-box">
                    <h1 class="u-heading-h1">
                        ウィキ
                    </h1>
                    <div class="c-box__btns">
                        <a class="c-btn --frame --bl u-mr20 back-btn" href="{{ route('admin.wiki.index') }}">{{ trans('messages.back') }}</a>
                        <a class="c-btn --bl wiki-add-btn" href="{{ route('admin.wiki.save') }}">{{ trans('messages.new_create') }}</a>
                    </div>
                </div>
                <div class="ttl-box">
                    <h2 class="w-breadcrumb">
                        @foreach ($breadcrumb_arr as $result)
                            @php
                                $breadcrumb = explode('/', $result['title']);
                                $breadcrumb = trim(end($breadcrumb), " ");
                            @endphp
                                @if ($result['id'] === $wiki->id)
                                    <span>{{ $breadcrumb }}</span>
                                    @break
                                @else
                                    <a href="{{ route('admin.wiki.detail', $result['id']) }}" class="breadcrumb">
                                        {{ $breadcrumb }}
                                    </a>
                                    <span> / </span>
                                @endif
                        @endforeach
                    </h2>
                    <div class="c-box__btns u-mt10 u-mb20 u-align--right">
                        <a class="c-btn --bl" href="{{ route('admin.wiki.save', $wiki->id) }}">{{ trans('messages.edit') }}</a>
                        @if ($wiki->id !== 1)
                            <a class="c-btn --frame --bl back-btn wiki-delete-link wiki-add-btn u-ml20" data-id="{{ $wiki->id }}"
                            data-target="#js-wiki-delete-modal">{{ trans('messages.delete') }}</a>
                        @endif
                    </div>
                </div>
                <div class="left-box ql-snow">
                    <div class="ql-editor">
                        <div>{!! $wiki->content !!}</div>
                        @php
                            $files = $wiki->file_path ? Storage::disk('public')->allFiles($wiki->file_path) : null;
                        @endphp
                        @if ($files)
                            <div class="file-box u-mb20">
                                @foreach ($files as $file)
                                    <div><a class="u-mb10" href="{{ route('admin.wiki.download', ['id' => $wiki->id, 'filepath' => basename($file)]) }}">{{ basename($file) }}</a></div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="c-modal c-modal--dialog js-modal" id="js-wiki-delete-modal">
                    <form id="wiki-delete-form-data" action="{{ route('admin.wiki.delete') }}" method="POST">
                        @csrf
                        <input type="hidden" id="wiki_id" name="id">
                        <div class="c-modal-container">
                            <div class="c-modal-header">
                                <h4 class="comment-modal-header">{{ trans('messages.wiki_delete') }}</h4>
                                <button type="button" class="comment-modal-close js-modal-btn--close" data-dismiss="modal"
                                    aria-label="close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <h4 class="c-modal-header__title --dialog u-mt50 u-mb45 u-fw--normal">{{ trans('messages.wiki_delete_message') }}</h4>
                            <div class="c-modal-body">
                                <ul class="c-modal-btns">
                                    <li class="c-modal-btns__item --dialog"><a
                                            class="c-btn --frame --bl js-modal-btn--close cancel-btn-border">{{ trans('messages.cancel') }}</a>
                                    </li>
                                    <li class="c-modal-btns__item --dialog"><button class="c-btn --bl" type="submit"
                                            id="js-answer-delete-modal-btn">{{ trans('messages.delete') }}</button></li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{-- // TODO:: To write right box code --}}
        </section>
        @foreach ($latest_modifier_arr as $key => $value)
            <div class="modifier-box u-mb15">
                <img class="modifier-img" src="{{ asset('img/admin/common/icon_user.svg') }}" alt="">
                <p class="u-ml10">modified by {{ $key }} at {{ displayDateTimeByTimeZone($value) }}.</p>
            </div>
        @endforeach
    </div>
@endsection

@section('script')
    @parent
    <script src="{{ asset('js/admin/wiki.js' . jsCssForceReload()) }}" defer></script>
@endsection
