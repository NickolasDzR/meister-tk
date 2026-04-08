@props(['value' => false])

<div {{ $attributes->merge(['class' => 'input _select ' . ($value['parentClass'] ?? '')]) }}>
    @if($value['icon'] ?? false)
        <x-icon :name="$value['icon']" :class="$value['iconClass'] ?? 'input__icon'" />
    @endif

    <select class="placeholder input__placeholder_select" name="{{ $value['name'] ?? '' }}">
        @foreach(($value['options'] ?? []) as $index => $option)
            <option value="{{ $index }}" data-select="{{ $option }}">{{ $option }}</option>
        @endforeach
    </select>

    <label class="label" data-title="{{ $value['options'][0] ?? '' }}" data-error="{{ $value['error'] ?? '' }}"></label>
</div>

{{--
-- Использование:
-- <x-select :value="[
--     'parentClass' => 'custom-select',
--     'icon' => 'arrow-down',
--     'iconClass' => 'select-icon',
--     'name' => 'city',
--     'options' => ['Москва', 'СПБ', 'Казань'],
--     'error' => 'Выберите город'
-- ]" />
--
-- Без иконки:
-- <x-select :value="[
--     'name' => 'country',
--     'options' => ['Россия', 'США', 'Китай']
-- ]" />
--}}