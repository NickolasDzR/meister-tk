@php
    $nav = [
        ['link' => route('posts'), 'text' => 'Новости'],
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

    $slides = $posts->map(fn ($post) => [
        'image' => $post->image,
        'image_mobile' => $post->image_mobile,
        'image_tablet' => $post->image_tablet,
        'title' => $post->title,
        'subtitle' => $post->excerpt ?? '',
        'link' => route('post', $post->slug),
    ])->all();
@endphp

@extends('layouts.app')

@section('title', 'Главная страница')

@section('content')
    @include('modules.header.header', ['nav' => $nav, 'soc' => $soc, 'contacts' => $contacts])

    <main>
        @include('modules.main-slider.main-slider', ['items' => $slides])
        @include('modules.forms.cargo-calc.cargo-calc', ['title' => 'Отправить груз', 'button' => 'Расчитать доставку'])
    </main>
    <x-button type="keppel" class="main__cargo-calc-button" text="Расчитать доставку" />
@endsection
