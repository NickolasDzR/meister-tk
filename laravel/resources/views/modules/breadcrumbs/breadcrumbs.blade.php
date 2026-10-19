{{--
-- Хлебные крошки: путь назад к разделам сайта. Текущую страницу не
-- повторяем — её заголовок и так стоит рядом. Тот же список целиком
-- (с текущей страницей) уходит в разметку modules/schema/breadcrumbs.
-- Использование:
-- @include('modules.breadcrumbs.breadcrumbs', ['items' => [
--     ['name' => 'Главная', 'url' => route('home')],
--     ['name' => 'Статьи', 'url' => route('posts')],
-- ], 'parentClass' => 'post__breadcrumbs'])
--}}
<nav class="breadcrumbs {{ $parentClass ?? '' }}" aria-label="Хлебные крошки">
    <ol class="breadcrumbs__list">
        @foreach($items as $item)
            <li class="breadcrumbs__item">
                <a class="breadcrumbs__link" href="{{ $item['url'] }}">{{ $item['name'] }}</a>
            </li>
        @endforeach
    </ol>
</nav>
