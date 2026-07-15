{{-- resources/views/components/graphic/remote-picture.blade.php --}}
@props(['value' => []])

@php
    use Illuminate\Support\Facades\Storage;

    $main = $value['main'] ?? null;
    $mobile = $value['mobile'] ?? $main;
    $tablet = $value['tablet'] ?? $main;
    $alt = $value['alt'] ?? '';
    $class = $value['class'] ?? '';

    $mobileUrl = $mobile ? Storage::disk('yandex')->url($mobile) : null;
    $tabletUrl = $tablet ? Storage::disk('yandex')->url($tablet) : null;
@endphp

@if($mobileUrl || $tabletUrl)
    <picture class="remote-picture">
        {{-- Планшет и десктоп (≥768px) --}}
        @if($tabletUrl)
            <source media="(min-width: 768px)" srcset="{{ $tabletUrl }}">
        @endif

        {{-- Мобильная версия (по умолчанию) / фолбек --}}
        <img
                class="{{ $class }}"
                src="{{ $mobileUrl ?? $tabletUrl }}"
                alt="{{ $alt }}"
                loading="lazy"
        >
    </picture>
@endif
