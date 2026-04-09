@props(['value' => false])

<div {{ $attributes->merge(['class' => 'contacts ' . ($parentClass ?? '')]) }}>
    <ul class="contacts__list">
        @foreach(($items ?? []) as $item)
            <li class="contacts__item">
                <a class="contacts__link-title" href="{{ $item['titleLink'] ?? '#' }}">{{ $item['title'] ?? '' }}</a>
                @if($item['subtitle'] ?? false)
                    <a class="contacts__link-subtitle" href="{{ $item['subtitleLink'] ?? '#' }}">{{ $item['subtitle'] }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</div>

{{--
-- Использование (модуль):
-- <x-contacts :value="[
--     'parentClass' => 'footer-contacts',
--     'items' => [
--         ['title' => 'Email', 'titleLink' => 'mailto:test@mail.com'],
--         ['title' => 'Телефон', 'titleLink' => 'tel:+71234567890', 'subtitle' => 'Пн-Пт 9-18', 'subtitleLink' => '/schedule'],
--         ['title' => 'Адрес', 'titleLink' => '/map']
--     ]
-- ]" />
--}}