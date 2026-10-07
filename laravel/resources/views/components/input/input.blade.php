@props(['placeholder' => '', 'error' => '', 'class' => '', 'placeholderClass' => '', 'icon' => '', 'iconClass' => '', 'name' => '', 'inputmode' => ''])

<div class="input {{ $class }}">
    @if($icon)
        <x-icon name="{{ $icon }}" class="input__icon" />
    @endif

    <input class="input__placeholder {{ $placeholderClass }}" name="{{ $name }}" placeholder="{{ $placeholder }}"@if($inputmode) inputmode="{{ $inputmode }}"@endif>

    <label class="input__label" data-title="{{ $placeholder }}" data-error="{{ $error }}"></label>
</div>