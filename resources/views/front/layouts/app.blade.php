<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ trans('generalConst.FRONT_APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    {{-- <link rel="favicon" href="/favicon.ico" /> --}}
    <meta name="format-detection" content="telephone=no" />
    <!-- Styles -->
    @include('front.includes.commons.style')
</head>

<body class="@yield('app')">
    <!-- ヘッダー -->
    @include('front.includes.commons.header')
    <!-- メインコンテンツ -->
    {{-- 画面に応じてmainの記載が異なるため各bladeにて指定が必要 （例：@section('main', '--floating')） --}}
    {{-- 追従コンテンツがある場合は --floating --}}
    {{-- エラー系ページは --error --}}
    {{-- 画面中央寄せのページは --centering --}}
    <main class="c-main @yield('main')" id="main">
        <!-- ページタイトル -->
        @yield('pageheader')
        <!-- step -->
        @yield('step')
        <!-- コンテンツ -->
        @yield('content')
    </main>
    @include('front.includes.commons.footer')
    <!-- ページモーダル-->
    @yield('page_modal')
    <!-- 追従コンテンツ-->
    @yield('floating_top')
    @yield('floating_insurance_fee')

    <!-- scripts-->
    @include('front.includes.commons.script')
    @section('script')

    @show
</body>

</html>