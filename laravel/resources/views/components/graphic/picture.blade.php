{{-- resources/views/components/graphic/picture.blade.php --}}
@props(['value' => []])

@php
    $name = $value['name'] ?? '';
    $alt = $value['alt'] ?? '';
    $class = $value['class'] ?? '';
    $md = $value['md'] ?? false;
    $lg = $value['lg'] ?? false;

    // Базовый путь к папке с картинками
    $basePath = "images/content/{$name}";
@endphp

<picture>
    {{-- Десктоп версия (≥992px) --}}
    @if($lg)
        <source
                media="(min-width: 992px)"
                srcset="{{ asset("{$basePath}-desk@1x.webp") }} 1x,
                    {{ asset("{$basePath}-desk@2x.webp") }} 2x"
                type="image/webp"
        >
    @endif

    {{-- Планшет версия (≥768px) --}}
    @if($md)
        <source
                media="(min-width: 768px)"
                srcset="{{ asset("{$basePath}-desk@1x.webp") }} 1x,
                    {{ asset("{$basePath}-desk@2x.webp") }} 2x"
                type="image/webp"
        >
    @endif

    {{-- Мобильная версия (по умолчанию) --}}
    <source
            srcset="{{ asset("{$basePath}@1x.webp") }} 1x,
                {{ asset("{$basePath}@2x.webp") }} 2x"
            type="image/webp"
    >

    {{-- Fallback для браузеров, не поддерживающих webp --}}
    <img
            class="{{ $class }}"
            src="{{ asset("{$basePath}@1x.webp") }}"
            srcset="{{ asset("{$basePath}@1x.webp") }} 1x,
                {{ asset("{$basePath}@2x.webp") }} 2x"
            alt="{{ $alt }}"
            loading="lazy"
    >
</picture>