@extends('admin.layouts.error')

@section('content')
<h1 class="p-MK2999__hdg u-mb40">{{ trans('error_msg.E503_msg01') }}</h1>
<p class="p-MK2999__txt u-mb90">{{ trans('error_msg.E503_msg02') }}<br>{{ trans('error_msg.E503_msg03') }}</p><a class="p-MK2999__btn c-btn --w200" href="{{route('admin.login')}}">{{ trans('messages.login_screen') }}</a>
@endsection
