@extends('admin.layouts.app')
@section('content')
    <div class="MKWIKI l-inner">
        <ul class="c-breadcrumb">
            {{-- // TODO:: To add top page route --}}
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="">{{ trans('messages.top') }}</a></li>
            <li class="c-breadcrumb__item"><a class="c-breadcrumb__link" href="">{{ trans('generalConst.WIKI') }}</a></li>
        </ul>
        <section class="u-mb40 wiki">
            <div class="left-main-box">
                <div class="ttl-box">
                    <h1 class="u-heading-h1">
                        {{ trans('generalConst.WIKI') }}
                    </h1>
                    <div class="c-box__btns">
                        @if ($wiki) <a class="c-btn --frame --bl back-btn u-mr20" href="{{ route('admin.wiki.save', $wiki->id) }}">{{ trans('messages.edit') }}</a> @endif
                        <a class="c-btn --bl wiki-add-btn" href="{{ route('admin.wiki.save') }}">{{ trans('messages.new_create') }}</a>
                    </div>
                </div>
                <div class="ttl-box">
                    <h2 class="w-breadcrumb u-mt20 u-mb20">
                        @if ($wiki) {{ $wiki->title }} @endif
                    </h2>
                </div>
                <div class="left-box ql-snow">
                    @if ($wiki)
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
                    @else
                        <p class="u-align--center u-mt20 u-mb20">{{ trans('messages.no_wiki_data') }}</p>
                    @endif
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
