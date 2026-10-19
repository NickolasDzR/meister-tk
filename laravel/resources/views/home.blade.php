@php
    $nav = [
        ['link' => route('posts'), 'text' => 'Статьи'],
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

@section('title', 'Грузоперевозки по России — ООО «Мейстер»')

@section('description', 'Грузоперевозки по России до 20 тонн: подбор транспорта, расчёт стоимости рейса онлайн, сопровождение документов.')

@section('og')
    <meta property="og:type" content="website">
    <meta property="og:title" content="Транспортная компания «Мейстер»">
    <meta property="og:description" content="Грузоперевозки по России: подбор транспорта, расчёт стоимости рейса, сопровождение документов.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('images/content/og-default.jpg') }}">
@endsection

@section('content')
    @include('modules.header.header', ['nav' => $nav])

    <main>
        @include('modules.main-slider.main-slider', [
            'items' => $slides,
            'cta' => $promo ? [
                'title' => $promo->title,
                'subtitle' => $promo->subtitle,
                'button' => $promo->button_text,
                'link' => $promo->link ?: route('posts'),
                'image' => $promo->image,
                'image_mobile' => $promo->image_mobile,
                'image_tablet' => $promo->image_tablet,
            ] : null,
        ])
        @include('modules.forms.cargo-calc.cargo-calc', [
            'title' => "Грузоперевозки по\u{00A0}России до\u{00A0}20\u{00A0}тонн",
            'button' => 'Рассчитать доставку',
        ])
    </main>
    <x-button type="keppel" class="main__cargo-calc-button" text="Рассчитать доставку" />
@endsection
