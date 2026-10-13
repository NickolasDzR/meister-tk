{{--
-- Круглая стрелка «назад / вперёд». Одна на весь сайт: слайдер «Другие
-- статьи», пагинация списка. Внешний вид — здесь и в arrow-button.scss,
-- расположение — в модуле, который её ставит (через class).
--
-- Тег выбирается по задаче:
--   href     → <a>, переход на другую страницу (пагинация);
--   disabled → <span> без ссылки: листать некуда, нажимать нечего;
--   иначе    → <button>, нажатие обрабатывает скрипт (Splide сам ставит
--              ему disabled на крайнем слайде).
-- Остальные атрибуты (rel, data-…) передаются на тег как есть.
--}}

@props(['direction' => 'next', 'href' => null, 'disabled' => false, 'label' => '', 'class' => ''])

@php
    $classes = implode(' ', array_filter([
        'arrow-button',
        'arrow-button_' . $direction,
        $disabled ? 'arrow-button_disabled' : null,
        $class,
    ]));

    $path = $direction === 'prev' ? 'M11 1L2 10L11 19' : 'M1 1L10 10L1 19';
@endphp

@if($disabled)
    <span class="{{ $classes }}" aria-hidden="true">
        <svg class="arrow-button__svg" viewBox="0 0 12 20" width="12" height="20" fill="none">
            <path class="arrow-button__path" d="{{ $path }}"/>
        </svg>
    </span>
@elseif($href)
    <a class="{{ $classes }}" href="{{ $href }}" aria-label="{{ $label }}" {{ $attributes }}>
        <svg class="arrow-button__svg" viewBox="0 0 12 20" width="12" height="20" fill="none">
            <path class="arrow-button__path" d="{{ $path }}"/>
        </svg>
    </a>
@else
    <button type="button" class="{{ $classes }}" aria-label="{{ $label }}" {{ $attributes }}>
        <svg class="arrow-button__svg" viewBox="0 0 12 20" width="12" height="20" fill="none">
            <path class="arrow-button__path" d="{{ $path }}"/>
        </svg>
    </button>
@endif

{{--
-- Использование:
-- <x-arrow-button direction="prev" class="slider-offer__arrow splide__arrow splide__arrow--prev" label="Назад" />
-- <x-arrow-button direction="next" :href="$paginator->nextPageUrl()" rel="next" label="Следующая страница" />
-- <x-arrow-button direction="prev" :disabled="true" />
--}}
