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

    $postCards = $posts->map(fn ($post) => [
        'image' => $post->image ?? 'post-cards/post-card-1',
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
    </main>

    @include('modules.footer.footer')
@endsection