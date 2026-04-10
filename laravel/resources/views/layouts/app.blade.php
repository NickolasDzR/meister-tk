<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csrf-param" content="_token"/>
    <meta name="ymap-api-key" content="{{ env('YMAP_API_KEY') }}">
    <!-- Bootstrap (оставим для сетки и утилит, но будем переопределять) -->
    @vite(['resources/ts/app.ts', 'resources/scss/app.scss'])

    @stack('styles')
    <title>@yield('title', 'Сайт компании ООО "Мейстер"')</title>
</head>
<body>
    @yield("content")
@stack('scripts')
</body>
</html>