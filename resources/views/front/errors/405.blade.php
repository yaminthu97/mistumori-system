@extends('front.layouts.error')

@section('content')
<div class="p-error">
    <h2 class="p-error__title">アクセスできないページです</h2>
    <p class="p-error__text">{!! __('error_msg.E090405') !!}</p>
    <p class="p-error__btn">
        <button class="c-btn --secondary" onclick="location.href='{{ route('front.top.index') }}'">TOPへ</button>
    </p>
</div>
@endsection
