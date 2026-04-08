@props(['value' => false])

@php
    $mod = isset($value['class']) ? $value['class'] . ' close-button is-active' : 'close-button is-active';
@endphp

<x-hamburger :class="$mod" />

{{--
-- Использование:
-- <x-close-button :value="['class' => 'my-custom-class']" />
--
-- Без параметров:
-- <x-close-button />
--
-- Результат: кнопке будет добавлен класс "close-button is-active"
-- и, если передан 'class', он добавится перед ними
--}}