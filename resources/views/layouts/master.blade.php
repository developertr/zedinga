<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zedinga</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAGTDzdpHzvHW_DEyQgRC_hA2-btaxypHs"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}" />
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<div id="app">
    <vue-snotify></vue-snotify>
    <loading
        :active.sync="isLoading"
        :can-cancel="false"
        :is-full-page="fullPage"
        :color="'#fff'"
        :width="50"
        :height="50"
        :background-color="'#000'"
        :opacity="0.8"
        :loader="'dots'"
    ></loading>
    @yield('content')
</div>
</body>
</html>
