@extends('front.layouts.error')

@section('content')
<div class="p-error">
    <h2 class="p-error__title">エラーが発生しました</h2>
    <p class="p-error__text">{!! $message !!}</p>
    <p class="p-error__btn">
        <button class="c-btn --secondary" onclick="location.href='{{ route('front.top.index') }}'">TOPへ</button>
    </p>
</div>
@endsection
