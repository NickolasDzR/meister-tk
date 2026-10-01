@php
    /*
    |--------------------------------------------------------------------------
    | Каталог стилей
    |--------------------------------------------------------------------------
    | Страница показывает типографику сайта настоящими классами из модулей —
    | не копиями. Поменяли размер в post.scss, обновили страницу — здесь новое
    | число. Цифры рядом с образцами снимает скрипт с самих элементов, руками
    | тут ничего не подписано.
    |
    | Отступы у образцов обнулены: каталог про шрифт, а не про расстояния.
    |
    | Когда разберём список ниже — удалить массив $todo и секцию «Что чинить».
    | Пометки у образцов исчезнут сами, они берутся из этого же массива.
    */

    $todo = [
        5 => [
            'title' => 'Заголовки из _global.scss ни на что не влияют',
            'what'  => '56, 40 и 24 пикселя — единственные размеры в проекте, заданные в rem. В макете их нет, на страницах их всюду перебивают классами. Доходят до экрана ровно в одном месте: «Другие статьи» внизу статьи, где поверх стоит инлайновый style="font-size: 30px".',
            'fix'   => 'Привести значения к реальным или убрать размеры совсем, а инлайновый стиль заменить классом.',
            'check' => 'В post.blade.php не осталось атрибута style.',
            'where' => '_global.scss, post.blade.php',
        ],
        6 => [
            'title' => 'Восемь межстрочных, две пары почти одинаковые',
            'what'  => '1.4 и 1.43 при кегле 16 дают 22.4 и 22.9 пикселя. 1.5 и 1.52 различаются так же незаметно. Похоже, их получали делением чисел из макета, а не брали из шкалы.',
            'fix'   => 'Свести каждую пару к одному значению.',
            'check' => 'В секции «Межстрочные» останется шесть карточек вместо восьми.',
            'where' => 'post.scss, main-slider.scss, cargo-calc.scss, contacts.scss, footer.scss',
        ],
        7 => [
            'title' => 'Вес 800 в одном месте на весь сайт',
            'what'  => 'Телефон в блоке контактов — единственное место с font-weight: 800. Остальной сайт обходится 300, 400 и 600.',
            'fix'   => 'Свериться с макетом: либо 600, либо осознанно оставить.',
            'check' => 'Образец .contacts__link-title показывает выбранный вес.',
            'where' => 'contacts.scss',
        ],
        9 => [
            'title' => 'Ссылки без цвета',
            'what'  => 'В _global.scss у ссылок стоит color: var(--color-accent), а такой переменной в палитре нет. Браузер считает такое объявление недействительным и берёт цвет по наследству, то есть правило не работает ни на одной странице. То же со вторым правилом — a:hover.',
            'fix'   => 'Решить, какой цвет у ссылки в тексте и при наведении, и задать его. Либо убрать оба правила, если ссылки везде красятся своими классами.',
            'check' => 'Ссылка внутри текста статьи отличается от обычного текста.',
            'where' => '_global.scss',
        ],
        8 => [
            'title' => 'font-weight: bold теперь рисуется другим начертанием',
            'what'  => 'Стоимость в расчёте рейса и текст прелоадера написаны через bold. Пока Bold и ExtraBold были объявлены одним весом, оба места рисовались ExtraBold`ом. Теперь это настоящий Bold, и начертание изменилось само собой.',
            'fix'   => 'Посмотреть на оба места и решить, тот ли это вес.',
            'check' => 'Расчёт рейса на главной и прелоадер выглядят как задумано.',
            'where' => 'cargo-calc.scss, preloader.scss',
        ],
    ];

    // Образцы. Каждый — настоящий класс сайта; dark — для белого текста,
    // который на светлом фоне не виден.
    $rows = [
        [
            'sel'   => '.post__title',
            'dark'  => true,
            'html'  => '<h1 class="post__title">Зимний рейс: что меняется в перевозках с ноября</h1>',
            'where' => 'заголовок на обложке статьи',
        ],
        [
            'sel'   => '.main-slider__title',
            'dark'  => true,
            'html'  => '<div class="main-slider__title">Доставим груз по России и СНГ</div>',
            'where' => 'первый экран главной — это div, не заголовок',
        ],
        [
            'sel'   => '.post__heading',
            'html'  => '<h2 class="post__heading">Что проверить перед выездом</h2>',
            'where' => 'заголовок раздела в статье',
        ],
        [
            'sel'   => '.post__excerpt',
            'html'  => '<p class="post__excerpt">Короткое описание статьи — показывается под обложкой и в карточке списка.</p>',
            'where' => 'лид статьи',
        ],
        [
            'sel'   => '.post-card__title',
            'dark'  => true,
            'html'  => '<p class="post-card__title">От гибридного «Урала» до нового российского пикапа</p>',
            'where' => 'карточка в списке статей — белый по фото, поэтому вес ниже',
        ],
        [
            'sel'   => '.slider-offer__title',
            'html'  => '<p class="slider-offer__title">Другие статьи по теме перевозок</p>',
            'where' => 'слайдер внизу статьи',
        ],
        [
            'sel'   => '.post__block-text',
            'html'  => '<div class="post__block-text">Основной текст статьи. Зимой маршрут меняется не только из-за погоды: часть дорог закрывают по весу, и объезд добавляет к рейсу сутки. Это закладывают в срок заранее, а не по факту.</div>',
            'where' => 'текст статьи',
            'todo'  => 2,
        ],
        [
            'sel'   => '.nav__link',
            'html'  => '<span class="nav__link">Статьи</span>',
            'where' => 'меню в шапке',
            'todo'  => 2,
        ],
        [
            'sel'   => '.input__placeholder',
            'html'  => '<span class="input__placeholder">Город отправки</span>',
            'where' => 'подпись над полем',
            'todo'  => 2,
        ],
        [
            'sel'   => '.post__italic',
            'html'  => '<p class="post__italic">Ремарка в сторону от основного рассказа — размер и цвет те же, отличие только в наклоне.</p>',
            'where' => 'блок «Ремарка»',
        ],
        [
            'sel'   => '.post-card__subtitle',
            'dark'  => true,
            'html'  => '<p class="post-card__subtitle">Описание в карточке: тот же размер, что у текста статьи, но межстрочный плотнее.</p>',
            'where' => 'карточка в списке статей',
        ],
        [
            'sel'   => '.main-slider__subtitle',
            'dark'  => true,
            'html'  => '<p class="main-slider__subtitle">Подпись под заголовком на первом экране.</p>',
            'where' => 'первый экран главной',
            'todo'  => 6,
        ],
        [
            'sel'   => '.post__quote cite',
            'html'  => '<blockquote class="post__quote" style="padding-left:0"><cite>Сергей Волков, руководитель автопарка</cite></blockquote>',
            'where' => 'автор цитаты',
            'todo'  => 6,
        ],
        [
            'sel'   => '.cargo-calc__title',
            'html'  => '<p class="cargo-calc__title">Рассчитать стоимость рейса</p>',
            'where' => 'форма на главной',
            'todo'  => 6,
        ],
        [
            'sel'   => '.contacts__link-title',
            'html'  => '<span class="contacts__link-title">+7 (999) 120 59 82</span>',
            'where' => 'контакты в шапке и подвале',
            'todo'  => 7,
        ],
        [
            'sel'   => '.footer__address',
            'html'  => '<span class="footer__address">ООО «Мейстер», транспортно-экспедиционные услуги</span>',
            'where' => 'подвал',
            'todo'  => 6,
        ],
        [
            'sel'   => '.button',
            'html'  => '<span class="button">Отправить груз</span>',
            'where' => 'кнопки по всему сайту',
        ],
        [
            'sel'   => '.pagination__link',
            'html'  => '<span class="pagination__link">12</span>',
            'where' => 'пагинация в списке',
        ],
    ];

    // Размеры, которые сейчас встречаются в стилях. orphan — встречается
    // ровно один раз и ни с чем не рифмуется.
    $scale = [
        ['size' => 56, 'weight' => 600, 'note' => 'h1 из _global.scss', 'orphan' => true],
        ['size' => 40, 'weight' => 600, 'note' => 'h2 из _global.scss, прелоадер', 'orphan' => true],
        ['size' => 36, 'weight' => 600, 'note' => 'заголовки от 768px'],
        ['size' => 32, 'weight' => 600, 'note' => 'прелоадер', 'orphan' => true],
        ['size' => 30, 'weight' => 600, 'note' => 'заголовок раздела в статье'],
        ['size' => 26, 'weight' => 600, 'note' => 'заголовки до 768px'],
        ['size' => 24, 'weight' => 600, 'note' => 'h3 из _global.scss', 'orphan' => true],
        ['size' => 20, 'weight' => 400, 'note' => 'лид, заголовки карточек'],
        ['size' => 18, 'weight' => 300, 'note' => 'подпись в слайдере, контакты'],
        ['size' => 16, 'weight' => 300, 'note' => 'основной текст, поля, кнопки'],
        ['size' => 14, 'weight' => 300, 'note' => 'подвал, автор цитаты'],
        ['size' => 12, 'weight' => 300, 'note' => 'ошибка под полем'],
        ['size' => 9,  'weight' => 300, 'note' => 'ошибка под полем до 768px', 'orphan' => true],
    ];

    $leading = [
        ['v' => '1',    'where' => 'пагинация',               'note' => 'ровно по кеглю'],
        ['v' => '1.2',  'where' => 'h1, h2, h3',              'note' => 'из _global.scss'],
        ['v' => '1.33', 'where' => 'заголовки, карточки',     'note' => 'самое частое'],
        ['v' => '1.4',  'where' => 'автор цитаты, селект',    'note' => '22.4px при кегле 16', 'todo' => 6],
        ['v' => '1.43', 'where' => 'слайдер, расчёт рейса',   'note' => '22.9px при кегле 16', 'todo' => 6],
        ['v' => '1.5',  'where' => 'подвал',                  'note' => '21px при кегле 14',   'todo' => 6],
        ['v' => '1.52', 'where' => 'контакты',                'note' => '27.4px при кегле 18', 'todo' => 6],
        ['v' => '1.6',  'where' => 'основной текст, меню',    'note' => '25.6px при кегле 16'],
    ];

    $faces = [
        ['w' => 300, 'name' => 'Light',      'file' => 'Geologica-Light.woff2'],
        ['w' => 400, 'name' => 'Regular',    'file' => 'Geologica-Regular.woff2'],
        ['w' => 600, 'name' => 'SemiBold',   'file' => 'Geologica-SemiBold.woff2'],
        ['w' => 700, 'name' => 'Bold',       'file' => 'Geologica-Bold.woff2',      'todo' => 1],
        ['w' => 800, 'name' => 'ExtraBold',  'file' => 'Geologica-ExtraBold.woff2', 'todo' => 1],
    ];
@endphp

@extends('layouts.app')

@section('title', 'Каталог стилей')

@section('content')
    <main class="sg">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    <header class="sg__head">
                        <p class="sg__eyebrow">Рабочая страница</p>
                        <h1 class="sg__title">Типографика</h1>
                        <p class="sg__lede">
                            Образцы собраны настоящими классами сайта, а не копиями стилей: поменяли размер в модуле — здесь новое число.
                            Цифры справа снимает скрипт с самих элементов, поэтому разойтись с кодом они не могут.
                            Отступы у образцов обнулены — каталог про шрифт, а не про расстояния.
                        </p>
                        <p class="sg__route">
                            <b>/styleguide</b>
                            <span>маршрут объявлен только при локальной среде, на сервере его не существует</span>
                        </p>
                    </header>

                    @if (!empty($todo))
                        <section class="sg__section">
                            <div class="sg__section-head">
                                <h2>Что чинить</h2>
                                <p>Пунктов: {{ count($todo) }}. Разберём — удалим эту секцию, страница останется просто каталогом.</p>
                            </div>

                            <ol class="sg__todo">
                                @foreach ($todo as $n => $item)
                                    <li class="sg__todo-item" id="todo-{{ $n }}">
                                        <span class="sg__todo-num">{{ $n }}</span>
                                        <div class="sg__todo-body">
                                            <h3>{{ $item['title'] }}</h3>
                                            <p>{{ $item['what'] }}</p>
                                            <dl class="sg__todo-meta">
                                                <dt>правка</dt><dd>{{ $item['fix'] }}</dd>
                                                <dt>проверка</dt><dd>{{ $item['check'] }}</dd>
                                                <dt>файлы</dt><dd><code>{{ $item['where'] }}</code></dd>
                                            </dl>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </section>
                    @endif

                    <section class="sg__section">
                        <div class="sg__section-head">
                            <h2>Начертания</h2>
                            <p>Пять файлов Geologica и веса, которыми они вызываются.</p>
                        </div>

                        <div class="sg__faces">
                            @foreach ($faces as $face)
                                <div class="sg__face">
                                    <div class="sg__face-word" style="font-weight: {{ $face['w'] }}">Перевозка сборных грузов</div>
                                    <div class="sg__face-meta">
                                        <b>{{ $face['w'] }} — {{ $face['name'] }}</b>
                                        <span>{{ $face['file'] }}</span>
                                        @isset($todo[$face['todo'] ?? 0])
                                            <a class="sg__flag" href="#todo-{{ $face['todo'] }}">пункт {{ $face['todo'] }}</a>
                                        @endisset
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="sg__section">
                        <div class="sg__section-head">
                            <h2>Стили по ролям</h2>
                            <p>Классы со страниц сайта. Белый текст показан на тёмной плашке — там, где он и живёт.</p>
                        </div>

                        <div class="sg__rows">
                            @foreach ($rows as $row)
                                <div class="sg__row">
                                    <div class="sg__sample @if(!empty($row['dark'])) sg__sample_dark @endif" data-sample>
                                        {!! $row['html'] !!}
                                    </div>
                                    <div class="sg__meta">
                                        <code class="sg__sel">{{ $row['sel'] }}</code>
                                        <dl class="sg__metrics"></dl>
                                        <p class="sg__where">{{ $row['where'] }}</p>
                                        @isset($todo[$row['todo'] ?? 0])
                                            <a class="sg__flag" href="#todo-{{ $row['todo'] }}">пункт {{ $row['todo'] }}</a>
                                        @endisset
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="sg__section">
                        <div class="sg__section-head">
                            <h2>Шкала размеров</h2>
                            <p>Значений: {{ count($scale) }}. Красным — те, что встречаются ровно один раз.</p>
                        </div>

                        <div class="sg__scale">
                            @foreach ($scale as $step)
                                <div class="sg__scale-row @if(!empty($step['orphan'])) sg__scale-row_orphan @endif">
                                    <span class="sg__scale-num">{{ $step['size'] }}</span>
                                    <span class="sg__scale-word" style="font-size: {{ $step['size'] }}px; font-weight: {{ $step['weight'] }}">Мейстер</span>
                                    <span class="sg__scale-note">{{ $step['note'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="sg__section">
                        <div class="sg__section-head">
                            <h2>Межстрочные</h2>
                            <p>Значений: {{ count($leading) }}.</p>
                        </div>

                        <div class="sg__leading">
                            @foreach ($leading as $lead)
                                <div class="sg__lead">
                                    <b>{{ $lead['v'] }}</b>
                                    <p>{{ $lead['where'] }}</p>
                                    <small>{{ $lead['note'] }}</small>
                                    @isset($todo[$lead['todo'] ?? 0])
                                        <a class="sg__flag" href="#todo-{{ $lead['todo'] }}">пункт {{ $lead['todo'] }}</a>
                                    @endisset
                                </div>
                            @endforeach
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </main>
@endsection
