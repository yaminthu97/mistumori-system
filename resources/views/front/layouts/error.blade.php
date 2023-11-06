<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>{{ trans('generalConst.FRONT_APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    {{-- <link rel="favicon" href="/favicon.ico"/> --}}
    <meta name="format-detection" content="telephone=no"/>
    <meta name="googlebot" content="noindex">
    <meta name="robots" content="noindex">

    <!-- Styles -->
    @include('front.includes.commons.style')
</head>
<body>
    <!-- ヘッダー -->
    @include('front.includes.commons.header')
    <!-- メインコンテンツ -->
    <main class="c-main --error" id="main">
        <!-- コンテンツ -->
        @yield('content')
    </main>
    <!-- フッター -->
    @include('front.includes.commons.footer')
    
    @include('front.includes.commons.script')
    @section('script')
    @show
</body>
</html>
