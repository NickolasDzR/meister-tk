@props(['value' => false])

<div class="post-card">
    <x-graphic.picture :value="[
        'name' => $card['image'],
        'alt' => $card['title'],
        'class' => 'post-card__image image',
    ]" />

    <a class="post-card__link" href="{{ $card['link'] ?? '#' }}">
        <div class="post-card__content">
            <p class="post-card__title">{{ $card['title'] ?? '' }}</p>
            <p class="post-card__subtitle">{{ $card['subtitle'] ?? '' }}</p>
        </div>
    </a>
</div>

{{--
-- Использование:
-- <x-post-card :value="[
--     'image' => 'post-image.jpg',
--     'link' => '/blog/post-1',
--     'title' => 'Заголовок поста',
--     'subtitle' => 'Краткое описание поста'
-- ]" />
--}}