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
                @if($paginator->onFirstPage())
                    <span class="pagination__arrow pagination__arrow_prev pagination__arrow_disabled" aria-hidden="true">
                        <svg class="pagination__svg" viewBox="0 0 12 20" width="12" height="20">
                            <path class="pagination__svg-path" d="M11.0283 1.20798L2.39014 9.84619L11.0283 18.4844"></path>
                        </svg>
                    </span>
                @else
                    <a class="pagination__arrow pagination__arrow_prev"
                       href="{{ $paginator->previousPageUrl() }}"
                       rel="prev"
                       aria-label="Предыдущая страница">
                        <svg class="pagination__svg" viewBox="0 0 12 20" width="12" height="20">
                            <path class="pagination__svg-path" d="M11.0283 1.20798L2.39014 9.84619L11.0283 18.4844"></path>
                        </svg>
                    </a>
                @endif
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
                @if($paginator->hasMorePages())
                    <a class="pagination__arrow pagination__arrow_next"
                       href="{{ $paginator->nextPageUrl() }}"
                       rel="next"
                       aria-label="Следующая страница">
                        <svg class="pagination__svg" viewBox="0 0 12 20" width="12" height="20">
                            <path class="pagination__svg-path" d="M1.39011 1.26797L10.0283 9.90619L1.39011 18.5444"></path>
                        </svg>
                    </a>
                @else
                    <span class="pagination__arrow pagination__arrow_next pagination__arrow_disabled" aria-hidden="true">
                        <svg class="pagination__svg" viewBox="0 0 12 20" width="12" height="20">
                            <path class="pagination__svg-path" d="M1.39011 1.26797L10.0283 9.90619L1.39011 18.5444"></path>
                        </svg>
                    </span>
                @endif
            </li>

        </ul>
    </nav>
@endif

{{--
-- Использование:
-- <x-pagination :paginator="$posts" />
--}}
