{{--
-- Страница ошибки: код, что случилось и куда идти дальше. Не тупик —
-- две дороги назад, на главную и к статьям.
-- Использование:
-- @include('modules.error.error', ['code' => 404, 'title' => '…', 'text' => '…'])
--}}
<section class="error">
    <div class="container">
        {{-- Знак с едущей дорогой и код ошибки — одной строкой. --}}
        <div class="error__hero" aria-hidden="true">
            @svg('logo-mark', 'error__mark')
            <p class="error__code">{{ $code }}</p>
        </div>
        <h1 class="error__title">{{ $title }}</h1>
        <p class="error__text">{{ $text }}</p>

        <div class="error__actions">
            <a class="button button_keppel" href="{{ route('home') }}">На главную</a>
            <a class="button button_transparent error__button-posts" href="{{ route('posts') }}">Статьи</a>
        </div>
    </div>
</section>
