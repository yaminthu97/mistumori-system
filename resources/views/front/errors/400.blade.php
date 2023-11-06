@extends('front.layouts.error')

@section('content')
<div class="p-error">
    <h2 class="p-error__title">システム処理中に<br class="view-sp">エラーが発生しました</h2>
    <p class="p-error__text">{!! __('error_msg.E090400') !!}</p>
    <p class="p-error__btn">
        <button class="c-btn --secondary" onclick="location.href='{{ route('front.top.index') }}'">TOPへ</button>
    </p>
</div>
@endsection
