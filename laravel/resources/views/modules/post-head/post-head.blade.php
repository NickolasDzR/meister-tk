{{--
-- TODO: модуль нигде не подключён и в текущем виде не работает.
-- 1) <x-picture> не резолвится: компонент лежит в components/graphic/,
--    то есть тег должен быть <x-graphic.picture>. Иначе — ошибка
--    "Unable to locate a class or view for component [picture]".
-- 2) Параметры переданы атрибутами (:name, :lg), а компонент объявляет
--    один проп value и читает $value['name'] — имя картинки не доходит,
--    путь собирается пустым.
-- Решить: удалить модуль (его работу делает разметка прямо в post.blade.php) или починить оба пункта.
--}}

@props(['value' => false])

<div class="post-head">
    <h1 class="title">{{ $value['title'] ?? '' }}</h1>

    <x-picture
            :name="$value['images']['mobile'] ?? ''"
            :lg="true"
            class="image"
    />
</div>

{{--
-- Использование (модуль):
-- <x-post-head :value="[
--     'title' => 'Заголовок статьи',
--     'images' => ['mobile' => 'post-header.jpg']
-- ]" />
--}}