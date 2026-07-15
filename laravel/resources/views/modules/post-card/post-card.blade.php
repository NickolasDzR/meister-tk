@props(['value' => false])

<div class="post-card">
    <x-graphic.remote-picture :value="[
        'main' => $card['image'] ?? null,
        'mobile' => $card['image_mobile'] ?? null,
        'tablet' => $card['image_tablet'] ?? null,
        'alt' => $card['title'] ?? '',
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
-- @include('modules.post-card.post-card', ['card' => [
--     'image' => 'posts/cover.webp',
--     'image_mobile' => 'posts/cover-mobile.webp',
--     'image_tablet' => 'posts/cover-tablet.webp',
--     'link' => '/posts/post-1',
--     'title' => 'Заголовок поста',
--     'subtitle' => 'Краткое описание поста'
-- ]])
--}}