@props(['value' => false, 'parentClass' => ''])

@php
    $svgClass = isset($value['svgClass']) ? 'logo__icon ' . $value['svgClass'] : 'logo__icon';
@endphp

<div {{ $attributes->merge(['class' => 'logo ' . $parentClass]) }}>
    @svg('logo', $svgClass)
</div>

{{--
-- Использование:
-- <x-logo :parentClass="'header__logo'" />
--
-- С кастомным классом для svg:
-- <x-logo :value="['svgClass' => 'custom-svg']" :parentClass="'footer__logo'" />
--}}