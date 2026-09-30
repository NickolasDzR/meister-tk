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
    {{--
    -- Шрифты первого экрана. Без preload браузер узнаёт о них только когда
    -- разберёт CSS — то есть поздно, и текст успевает моргнуть запасным
    -- шрифтом. Здесь только те начертания, что видны сразу: Regular — весь
    -- текст, SemiBold — заголовок слайда.
    -- crossorigin обязателен даже для своего домена: шрифты грузятся в
    -- анонимном режиме, и без него файл скачается вторым запросом.
    -- Формат обязан совпадать с тем, что первым запросит CSS (сейчас woff2),
    -- иначе файл скачается дважды.
    --}}
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/Geologica-Regular.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/Geologica-SemiBold.woff2') }}" crossorigin>

    @vite(['resources/ts/app.ts', 'resources/scss/app.scss'])

    <!-- Ставим стили и скрипты определенной страницы -->
    @php $page = Route::currentRouteName() ?? 'home'; @endphp
    @vite(["resources/scss/pages/{$page}.scss", "resources/ts/pages/{$page}.ts"])

    @stack('styles')
    <title>@yield('title', 'Сайт компании ООО «Мейстер»')</title>

    {{--
    -- Open Graph: из этих тегов телеграм, вк и прочие собирают карточку,
    -- когда кто-то кидает ссылку. Страница может задать свои через
    -- @section('og'); если не задала — показываем общие для сайта.
    -- Адреса обязательно абсолютные: мессенджер читает теги в отрыве
    -- от страницы и относительный путь не разберёт.
    --}}
    <meta property="og:site_name" content="Мейстер ТК">
    <meta property="og:locale" content="ru_RU">
    <meta name="twitter:card" content="summary_large_image">

    @hasSection('og')
        @yield('og')
    @else
        <meta property="og:type" content="website">
        <meta property="og:title" content="@yield('title', 'Транспортная компания «Мейстер»')">
        <meta property="og:description" content="Грузоперевозки по России: подбор транспорта, расчёт стоимости рейса, сопровождение документов.">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ asset('images/content/og-default.jpg') }}">
    @endif
</head>
<body>
    @yield("content")
@stack('scripts')
</body>
</html>