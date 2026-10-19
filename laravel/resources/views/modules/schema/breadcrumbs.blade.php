{{--
-- Разметка Schema.org: путь до страницы. Из неё поисковик рисует под
-- ссылкой цепочку «Главная › Статьи › …» вместо голого адреса.
-- $items — пары «название, адрес» от главной до текущей страницы.
-- Использование:
-- @include('modules.schema.breadcrumbs', ['items' => [
--     ['name' => 'Главная', 'url' => route('home')],
--     ['name' => 'Статьи', 'url' => route('posts')],
-- ]])
--}}
@php
    $breadcrumbs = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => collect($items)->values()->map(fn ($item, $index) => [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $item['name'],
            'item' => $item['url'],
        ])->all(),
    ];
@endphp
<script type="application/ld+json">
@json($breadcrumbs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
</script>
