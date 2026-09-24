@php
    $nav = [
        ['link' => '#', 'text' => 'Новости'],
        ['link' => '#', 'text' => 'Расчитать доставку'],
        ['link' => '#', 'text' => 'Контакты']
    ];

    $soc = [
        ['link' => '#', 'name' => 'tg'],
        ['link' => '#', 'name' => 'vk']
    ];

    $contacts = [
        [
            'title' => '+7 (999) 120 59 82',
            'titleLink' => '#',
            'subtitle' => 'nickolasdzr@yandex.ru',
            'subtitleLink' => '#'
        ]
    ];

    // getCollection(), а не сам пагинатор: карточкам нужен плоский массив,
    // а $posts должен остаться пагинатором — из него рисуются ссылки страниц.
    $postCards = $posts->getCollection()->map(fn ($post) => [
        'image' => $post->image,
        'image_mobile' => $post->image_mobile,
        'image_tablet' => $post->image_tablet,
        'link' => route('post', $post->slug),
        'title' => $post->title,
        'subtitle' => $post->excerpt ?? '',
    ])->toArray();
@endphp

@extends('layouts.app')

@section('title', 'Список постов')

@section('content')
    @include('modules.header.header', ['nav' => $nav, 'soc' => $soc, 'contacts' => $contacts])

    <main>
        @include('modules.post-cards.post-cards', ['postCards' => $postCards])

        <div class="container">
            <div class="row">
                <div class="col-12">
                    <x-pagination :paginator="$posts" />
                </div>
            </div>
        </div>
    </main>

    @include('modules.footer.footer')
@endsection