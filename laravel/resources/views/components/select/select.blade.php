@props(['options' => [], 'error' => '', 'parentClass' => '', 'icon' => '', 'name' => ''])

<div class="input input_select {{ $parentClass }}">
    @if($icon)
        <x-icon name="{{ $icon }}" class="input__icon" />
    @endif

    <select class="input__placeholder input__placeholder_select" name="{{ $name }}">
        @foreach($options as $index => $option)
            <option value="{{ $index }}" data-select="{{ $option }}">{{ $option }}</option>
        @endforeach
    </select>

    <label class="input__label" data-title="{{ $options[0] ?? '' }}" data-error="{{ $error }}"></label>
</div>