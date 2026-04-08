@props(['value' => false])

<button type="button" {{ $attributes->merge(['class' => 'hamburger ' . ($value['class'] ?? '')]) }}>
    <span class="hamburger-box">
        <span class="hamburger-inner"></span>
    </span>
</button>

{{--
-- Использование:
-- <x-hamburger :value="['class' => 'additional-class']" />
--
-- Или через атрибуты:
-- <x-hamburger class="my-custom-class" />
--
-- Без параметров:
-- <x-hamburger />
--}}