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