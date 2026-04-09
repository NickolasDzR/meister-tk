@props(['value' => false, 'parentClass' => ''])

@php
    $socItems = $items ?? [];
    $svgClassBase = $svgClass ?? '';
@endphp

<div {{ $attributes->merge(['class' => 'soc ' . $parentClass]) }}>
    <ul class="soc__list">
        @foreach($socItems as $item)
            <li class="soc__item">
                <a class="soc__link" href="{{ $item['link'] ?? '#' }}">
                    <x-icon
                        :name="$item['name'] ?? ''"
                        :class="$svgClassBase ? 'soc__svg ' . $svgClassBase . ' soc__svg_' . ($item['name'] ?? '') : 'soc__svg soc__svg_' . ($item['name'] ?? '')"
                    />
                </a>
            </li>
        @endforeach
    </ul>
</div>

{{--
-- Использование:
-- <x-soc :parentClass="'footer__soc'" :value="[
--     'svgClass' => 'custom-svg',
--     'items' => [
--         ['name' => 'vk', 'link' => 'https://vk.com/...'],
--         ['name' => 'tg', 'link' => 'https://t.me/...'],
--         ['name' => 'whatsapp', 'link' => 'https://wa.me/...']
--     ]
-- ]" />
--
-- Без дополнительных классов:
-- <x-soc :value="['items' => [['name' => 'fb', 'link' => '#']]]" />
--}}