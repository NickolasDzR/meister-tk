@props(['postCards' => false])

<div class="post-cards">
    <ul class="list">
        @foreach($postCards as $card)
            <li class="item">
                <x-post-card :value="$card" />
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