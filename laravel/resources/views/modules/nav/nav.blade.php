@props(['value' => false, 'parentClass' => '', 'items' => []])

@php
    $navItems = $value['items'] ?? $items;
@endphp

<nav {{ $attributes->merge(['class' => 'nav ' . $parentClass]) }}>
    <ul class="nav__list">
        @foreach($navItems as $item)
            <li class="nav__item">
                <a class="nav__link" href="{{ $item['link'] ?? '#' }}">{{ $item['text'] ?? '' }}</a>
            </li>
        @endforeach
    </ul>
</nav>

{{--
-- Использование:
-- <x-nav :parentClass="'header__nav'" :items="[
--     ['text' => 'Главная', 'link' => '/'],
--     ['text' => 'О нас', 'link' => '/about'],
--     ['text' => 'Контакты', 'link' => '/contacts']
-- ]" />
--
-- Или через value:
-- <x-nav :value="['items' => $menuItems]" parentClass="footer__nav" />
--}}