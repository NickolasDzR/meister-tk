{{--
-- TODO: модуль нигде не подключён и в текущем виде не работает.
-- 1) <x-picture> не резолвится: компонент лежит в components/graphic/,
--    то есть тег должен быть <x-graphic.picture>. Иначе — ошибка
--    "Unable to locate a class or view for component [picture]".
-- 2) Параметры переданы атрибутами (:name, :lg), а компонент объявляет
--    один проп value и читает $value['name'] — имя картинки не доходит,
--    путь собирается пустым.
-- Решить: удалить модуль (его работу делает модуль post-cards) или починить оба пункта.
--}}

@props(['val' => false])

<ul class="post-list">
    @foreach($val as $post)
        <li class="item">
            <a class="link" href="{{ $post['link'] ?? '#' }}">
                <x-picture
                        :name="$post['images']['tablet'] ?? ''"
                        class="image"
                />
                <p class="title _top">{{ $post['title'] ?? '' }}</p>
                <div class="title-inner">
                    <div class="title-wrapper">
                        <p class="title">{{ $post['title'] ?? '' }}</p>
                        <p class="subtitle">{{ $post['subtitle'] ?? '' }}</p>
                    </div>
                </div>
            </a>
        </li>
    @endforeach
</ul>

{{--
-- Использование (модуль):
-- <x-post-list :val="[
--     [
--         'link' => '/post/1',
--         'images' => ['tablet' => 'post-1.jpg'],
--         'title' => 'Заголовок поста',
--         'subtitle' => 'Подзаголовок поста'
--     ],
--     [
--         'link' => '/post/2',
--         'images' => ['tablet' => 'post-2.jpg'],
--         'title' => 'Второй пост',
--         'subtitle' => 'Описание второго поста'
--     ]
-- ]" />
--}}