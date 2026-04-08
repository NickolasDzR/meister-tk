@props(['value' => false])

@php
    $mod = isset($value['type']) ? 'button_' . $value['type'] : '';
    $el = isset($value['class']) ? $value['class'] : '';
@endphp

<button class="{{ trim($mod . ' ' . $el) }}">{{ $value['text'] ?? '' }}</button>

{{--
-- <x-button :value="['type' => 'primary', 'class' => 'btn', 'text' => 'Click me']" />
--}}