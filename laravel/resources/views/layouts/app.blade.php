<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    {{-- Без запрета масштабирования: человек должен мочь увеличить текст пальцами. --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', 'Грузоперевозки по России до 20 тонн: подбор транспорта, расчёт стоимости рейса, сопровождение документов.')">

    {{--
    -- Пока сайт закрыт от поисковиков (SITE_INDEXING=false), каждая страница
    -- просит не попадать в выдачу. Открывается одной строкой в .env,
    -- вместе с robots.txt (маршрут в routes/web.php).
    --}}
    @unless(config('site.indexing'))
        <meta name="robots" content="noindex, nofollow">
    @endunless

    <link rel="icon" href="/favicon.ico" sizes="32x32">
    <link rel="icon" href="/icon-192.png" type="image/png" sizes="192x192">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csrf-param" content="_token"/>
    <meta name="ymap-api-key" content="{{ config('services.yandex_maps.api_key') }}">
    {{--
    -- Шрифты первого экрана. Без preload браузер узнаёт о них только когда
    -- разберёт CSS — то есть поздно, и текст успевает моргнуть запасным
    -- шрифтом. Здесь только те начертания, что видны сразу: Regular — весь
    -- текст, SemiBold — заголовок слайда.
    -- crossorigin обязателен даже для своего домена: шрифты грузятся в
    -- анонимном режиме, и без него файл скачается вторым запросом.
    -- Формат обязан совпадать с тем, что первым запросит CSS (сейчас woff2),
    -- иначе файл скачается дважды.
    -- Адрес — через Vite::asset, тот же, что в собранном CSS. С asset('fonts/…')
    -- предзагрузка тянула копию из public/fonts, а CSS — файл из build/assets
    -- с хешем в имени: разные адреса, шрифт качался дважды.
    --}}
    <link rel="preload" as="font" type="font/woff2" href="{{ Vite::asset('resources/fonts/Geologica-Regular.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ Vite::asset('resources/fonts/Geologica-SemiBold.woff2') }}" crossorigin>

    @vite(['resources/ts/app.ts', 'resources/scss/app.scss'])

    <!-- Ставим стили и скрипты определенной страницы -->
    {{-- Страница без маршрута (ошибка 404, 500) называет свой набор сама через @section('page'). --}}
    @php $page = trim($__env->yieldContent('page')) ?: (Route::currentRouteName() ?? 'home'); @endphp
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
    <meta property="og:site_name" content="{{ config('site.name') }}">
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

    @include('modules.schema.organization')
    @stack('schema')
</head>
<body>
    @yield("content")
@stack('scripts')
</body>
</html>