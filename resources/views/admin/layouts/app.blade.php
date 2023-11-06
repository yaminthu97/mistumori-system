<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ trans('generalConst.ADMIN_APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    {{-- <link rel="favicon" href="/favicon.ico" /> --}}
    <meta name="format-detection" content="telephone=no" />
    <meta name="googlebot" content="noindex">
    <meta name="robots" content="noindex">

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    @include('admin.includes.commons.style')
</head>

<body>
    <!-- ダークモードの設定 -->
    <script>
        var currentTheme = localStorage.getItem('theme');

        if (currentTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            document.documentElement.classList.toggle('dark');
        }
    </script>
    <!-- ヘッダー-->
    @include('admin.includes.commons.header')
    <!-- ./ ヘッダー-->
    <!-- サイドバー-->
    @include('admin.includes.commons.sidebar')
    <!-- ./ サイドバー-->
    <!-- メインコンテンツ -->
    <main class="c-main c-main--floating" id="main">
        @yield('content')
    </main>
    <!-- ヘッダーメニューモーダル-->
    @include('admin.includes.modals.header_modal')
    <!-- ページモーダル-->
    @yield('page_modal')
    <!-- scripts-->
    @include('admin.includes.commons.script')
    @section('script')

    @show
</body>

</html>
