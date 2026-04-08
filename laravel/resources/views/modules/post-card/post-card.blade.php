@props(['value' => false])

<div class="post-card">
    <x-picture :name="$value['image'] ?? ''" class="image" />

    <a class="link" href="{{ $value['link'] ?? '#' }}">
        <div class="content">
            <p class="title">{{ $value['title'] ?? '' }}</p>
            <p class="subtitle">{{ $value['subtitle'] ?? '' }}</p>
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