@props(['class' => ''])

@php
    $mod = trim($class . ' close-button is-active');
@endphp

<x-hamburger class="{{ $mod }}" />