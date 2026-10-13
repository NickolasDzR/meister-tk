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
            {{-- Прозрачная кнопка с рамкой, как «Подробнее» в слайдере на главной,
                 но маленькая: тут она на каждой карточке. Тег span, а не button:
                 карточка уже ссылка, и вторая кнопка внутри неё была бы лишней
                 для клавиатуры и скринридера. Нажимается вся карточка. --}}
            <span class="button button_transparent button_small post-card__more">Читать статью<x-icon name="thin-arrow-right" class="post-card__more-arrow" aria-hidden="true" /></span>
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