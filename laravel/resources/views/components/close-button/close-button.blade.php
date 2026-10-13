@props(['class' => ''])

@php
    $mod = trim($class . ' close-button is-active');
@endphp

{{-- Остальные атрибуты (aria-label и т. п.) — на кнопку как есть. --}}
<x-hamburger class="{{ $mod }}" {{ $attributes }} />