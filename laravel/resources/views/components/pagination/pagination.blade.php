{{--
-- TODO (когда статей станет много, страниц больше ~10):
-- 1) Сейчас выводятся ВСЕ номера страниц. При 99 страницах это 99 ссылок
--    в разметке на каждой странице. Нужен оконный вывод с многоточием.
--    Не изобретать: в Laravel уже есть Illuminate\Pagination\UrlWindow —
--    до 13 страниц он показывает все подряд (поэтому вопрос «а если их 4»
--    отпадает сам), дальше рисует первые две, многоточие, окно вокруг
--    текущей, многоточие, последние две. Подключается через
--    $paginator->onEachSide(1)->links('components.pagination.pagination'),
--    и во вьюху приезжает готовый массив $elements.
-- 2) Когда появится фильтр («Сначала старые», «Популярные»), в маршрут
--    нужно добавить ->withQueryString(), иначе клик по номеру страницы
--    молча сбросит фильтр на умолчание.
--}}

@props(['paginator' => null])

{{-- Одна страница — листать нечего, разметку не выводим вовсе. --}}
@if($paginator && $paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Постраничная навигация">
        <ul class="pagination__list">

            {{-- Назад --}}
            <li class="pagination__item">
                <x-arrow-button direction="prev"
                                :href="$paginator->previousPageUrl()"
                                :disabled="$paginator->onFirstPage()"
                                rel="prev"
                                label="Предыдущая страница" />
            </li>

            {{-- Номера страниц --}}
            @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                <li class="pagination__item">
                    @if($page == $paginator->currentPage())
                        <span class="pagination__link pagination__link_active" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="pagination__link" href="{{ $url }}">{{ $page }}</a>
                    @endif
                </li>
            @endforeach

            {{-- Вперёд --}}
            <li class="pagination__item">
                <x-arrow-button direction="next"
                                :href="$paginator->nextPageUrl()"
                                :disabled="! $paginator->hasMorePages()"
                                rel="next"
                                label="Следующая страница" />
            </li>

        </ul>
    </nav>
@endif

{{--
-- Использование:
-- <x-pagination :paginator="$posts" />
--}}
