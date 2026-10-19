@php
    $nav = [
        ['link' => route('home'), 'text' => 'Главная'],
        ['link' => route('posts'), 'text' => 'Статьи'],
    ];

    // Путь до статьи: целиком — в разметку для поисковика, без самой
    // статьи — в видимые крошки (её заголовок стоит рядом).
    $breadcrumbs = [
        ['name' => 'Главная', 'url' => route('home')],
        ['name' => 'Статьи', 'url' => route('posts')],
        ['name' => $post->title, 'url' => route('post', $post->slug)],
    ];
@endphp

@extends('layouts.app')

@section('title', $post->title)

@push('schema')
    @include('modules.schema.article')
    @include('modules.schema.breadcrumbs', ['items' => $breadcrumbs])
@endpush

@section('og')
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $post->title }}">
    <meta property="og:description" content="{{ $post->excerpt ?? 'Статья компании «Мейстер»' }}">
    <meta property="og:url" content="{{ route('post', $post->slug) }}">
    @if($post->ogImageUrl())
        <meta property="og:image" content="{{ $post->ogImageUrl() }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif
    @if($post->published_at)
        <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    @endif
@endsection

@section('content')
    @include('modules.header.header', ['nav' => $nav])
    <main>
        <article class="post">
            <header class="post__header">
                @if($post->image)
                    <div class="post__cover">
                        <x-graphic.remote-picture :value="[
                            'main' => $post->image,
                            'mobile' => $post->image_mobile,
                            'tablet' => $post->image_tablet,
                            'alt' => $post->title,
                            'class' => 'post__head-img',
                        ]" />

                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <h1 class="post__title">{{ $post->title }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="container relative">
                    <div class="row">
                        <div class="col-12">
                            @include('modules.breadcrumbs.breadcrumbs', [
                                'items' => array_slice($breadcrumbs, 0, -1),
                                'parentClass' => 'post__breadcrumbs',
                            ])

                            <div class="post__meta">
                                @if($post->published_at)
                                    <time class="post__date" datetime="{{ $post->published_at->format('Y-m-d') }}">
                                        {{ $post->published_at->translatedFormat('d F Y') }}
                                    </time>
                                @endif

                                {{-- Подпись словами — для экранного диктора: глаз он не прочтёт. --}}
                                <span class="post__views" title="Просмотры">
                                    <x-icon name="eye" class="post__views-icon" aria-hidden="true" />
                                    <span class="visually-hidden">Просмотры:</span>
                                    {{ number_format($post->views, 0, '', ' ') }}
                                </span>
                            </div>

                            @if($post->excerpt)
                                <p class="post__excerpt">{{ $post->excerpt }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="post__content">
                            @foreach($post->content ?? [] as $block)
                                @switch($block['type'])

                                    {{-- Текст --}}
                                    @case('text')
                                        <div class="post__block">
                                            <div class="post__block-text">
                                                {!! $block['data']['content'] !!}
                                            </div>
                                        </div>
                                    @break

                                    {{-- Картинка + Текст --}}
                                    @case('image_with_text')
                                        <div class="post__block">
                                            <div class="post__block-image-with-text">
                                                <div class="post__block-image">
                                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('yandex')->url($block['data']['image']) }}" alt="" @if(!empty($block['data']['zoomable'])) data-zoom @endif>
                                                </div>
                                                <div class="post__block-text post__block-text_image">
                                                    <p>{{ $block['data']['text'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @break

                                    {{-- Заголовок h2 --}}
                                    @case('heading')
                                        <div class="post__block post__block_heading">
                                            <h2 class="post__heading">{{ $block['data']['text'] }}</h2>
                                        </div>
                                    @break

                                    {{-- Цитата --}}
                                    @case('quote')
                                        <div class="post__block">
                                            <blockquote class="post__quote">
                                                <x-icon name="quotes" class="post__quote-svg"></x-icon>
                                                <p>{{ $block['data']['text'] }}</p>
                                                @if(!empty($block['data']['author']))
                                                    <cite>{{ $block['data']['author'] }}</cite>
                                                @endif
                                            </blockquote>
                                        </div>
                                    @break

                                    {{-- Маркированный список --}}
                                    @case('unordered_list')
                                        <div class="post__block">
                                            <ul class="post__list">
                                                @foreach($block['data']['items'] ?? [] as $item)
                                                    <li class="post__list-item">
                                                        {{ $item['text'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @break

                                    {{-- Нумерованный список --}}
                                    @case('ordered_list')
                                        <div class="post__block">
                                            <ol class="post__list">
                                                @foreach($block['data']['items'] ?? [] as $item)
                                                    <li class="post__list-item">{{ $item['text'] }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    @break

                                    {{-- Две картинки. data-zoom со значением — номер блока: картинки
                                         с одинаковым значением листаются в окне вместе, а с другими
                                         блоками статьи не смешиваются. --}}
                                    @case('two_images')
                                        <div class="post__block">
                                            <div class="post__two-images">
                                                <div class="post__two-images-image">
                                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('yandex')->url($block['data']['image_left']) }}" alt="" @if(!empty($block['data']['zoomable'])) data-zoom="block-{{ $loop->index }}" @endif>
                                                </div>
                                                <div class="post__two-images-image">
                                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('yandex')->url($block['data']['image_right']) }}" alt="" @if(!empty($block['data']['zoomable'])) data-zoom="block-{{ $loop->index }}" @endif>
                                                </div>
                                            </div>
                                        </div>
                                    @break

                                    {{-- Врезка --}}
                                    @case('callout')
                                        <div class="post__block">
                                            <p class="post__callout">{{ $block['data']['text'] }}</p>
                                        </div>
                                    @break

                                    {{-- Ремарка --}}
                                    @case('italic')
                                        <div class="post__block">
                                            <p class="post__italic">{{ $block['data']['text'] }}</p>
                                        </div>
                                    @break

                                    {{-- Разделитель (hr) --}}
                                    @case('divider')
                                        <div class="post__block">
                                            <hr>
                                        </div>
                                    @break

                                @endswitch
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </article>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @include('modules.slider-offer.slider-offer')
                </div>
            </div>
        </div>
    </main>

    @include('modules.footer.footer')
@endsection
