@props(['postCards' => false])

<div class="post-cards">
    <ul class="post-cards__list">
        @foreach($postCards as $card)
            <li class="post-cards__item">
                @include('modules.post-card.post-card', ['card' => $card])
            </li>
        @endforeach
    </ul>
</div>

{{--
-- Использование (модуль):
-- <x-post-cards :postCards="[
--     [
--         'image' => 'post-1.jpg',
--         'link' => '/blog/1',
--         'title' => 'Первый пост',
--         'subtitle' => 'Описание первого поста'
--     ],
--     [
--         'image' => 'post-2.jpg',
--         'link' => '/blog/2',
--         'title' => 'Второй пост',
--         'subtitle' => 'Описание второго поста'
--     ]
-- ]" />
--}}