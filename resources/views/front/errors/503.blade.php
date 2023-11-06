@extends('front.layouts.error')

@section('content')
<div class="p-error">
    <h2 class="p-error__title">システムメンテナンス</h2>
    <p class="p-error__text">{!! __('error_msg.E090503') !!}</p>
    <dl class="p-error-term">
    <dt class="p-error-term__title">メンテナンス予定時間</dt>
        <dd class="p-error-term__body">2022/11/01/10:00 <br class="view-sp">〜 <br class="view-sp">2022/11/01/11:00</dd>
    </dl>
</div>
@endsection
