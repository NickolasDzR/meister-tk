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
@endphp

@extends('layouts.app')

@section('title', $post->title)

@section('content')
    @include('modules.header.header', ['nav' => $nav, 'soc' => $soc, 'contacts' => $contacts])

    <main>
        <article class="post">
            <header class="post__header">
                @if($post->image)
                    <div class="post__cover">
                        <img class="post__head-img" src="{{ \Illuminate\Support\Facades\Storage::disk('yandex')->url($post->image) }}" alt="{{ $post->title }}">

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
                            @if($post->published_at)
                                <time class="post__date" datetime="{{ $post->published_at->format('Y-m-d') }}">
                                    {{ $post->published_at->translatedFormat('d F Y') }}
                                </time>
                            @endif

                            @if($post->excerpt)
                                <p class="post__excerpt">{{ $post->excerpt }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <div class="container">

                <div class="post__content">
                    @foreach($post->content ?? [] as $block)
                        @switch($block['type'])

                            {{-- Текст --}}
                            @case('text')
                                <div class="post__block-text">
                                    {!! $block['data']['content'] !!}
                                </div>
                            @break

                            {{-- Картинка + Текст --}}
                            @case('image_with_text')
                                <div class="post__block-image-with-text">
                                    <div class="post__block-image">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('yandex')->url($block['data']['image']) }}" alt="">
                                    </div>
                                    <div class="post__block-text post__block-text_image">
                                        <p>{{ $block['data']['text'] }}</p>
                                    </div>
                                </div>
                            @break

                            {{-- Заголовок h2 --}}
                            @case('heading')
                                <div class="post__block post__block--heading">
                                    <h2>{{ $block['data']['text'] }}</h2>
                                </div>
                            @break

                            {{-- Цитата --}}
                            @case('quote')
                                <div class="post__block post__block--quote">
                                    <blockquote>
                                        <p>{{ $block['data']['text'] }}</p>
                                        @if(!empty($block['data']['author']))
                                            <cite>{{ $block['data']['author'] }}</cite>
                                        @endif
                                    </blockquote>
                                </div>
                            @break

                            {{-- Маркированный список --}}
                            @case('unordered_list')
                                <div class="post__block post__block--list">
                                    <ul>
                                        @foreach($block['data']['items'] ?? [] as $item)
                                            <li>{{ $item['text'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @break

                            {{-- Нумерованный список --}}
                            @case('ordered_list')
                                <div class="post__block post__block--list post__block--list-ordered">
                                    <ol>
                                        @foreach($block['data']['items'] ?? [] as $item)
                                            <li>{{ $item['text'] }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @break

                            {{-- Две картинки --}}
                            @case('two_images')
                                <div class="post__block post__block--two-images">
                                    <div class="post__block-image">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('yandex')->url($block['data']['image_left']) }}" alt="">
                                    </div>
                                    <div class="post__block-image">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('yandex')->url($block['data']['image_right']) }}" alt="">
                                    </div>
                                </div>
                            @break

                            {{-- Врезка --}}
                            @case('callout')
                                <div class="post__block post__block--callout">
                                    @if(!empty($block['data']['title']))
                                        <p class="post__block-callout-title">{{ $block['data']['title'] }}</p>
                                    @endif
                                    <p>{{ $block['data']['text'] }}</p>
                                </div>
                            @break

                            {{-- Курсив (em) --}}
                            @case('italic')
                                <div class="post__block post__block--italic">
                                    <em>{{ $block['data']['text'] }}</em>
                                </div>
                            @break

                            {{-- Разделитель (hr) --}}
                            @case('divider')
                                <div class="post__block post__block--divider">
                                    <hr>
                                </div>
                            @break

                        @endswitch
                    @endforeach
                </div>

            </div>
        </article>
    </main>

    @include('modules.footer.footer')
@endsection
