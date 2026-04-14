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

    $slides = [
        [
            'images' => [
                'mobile' => 'slider/slide-1',
                'tablet' => 'slider/slide-1-desk',
            ],
            'title' => 'Обманчивый штиль: как меняются ставки на автоперевозки в середине лета',
            'subtitle' => 'Рассказываем, на каких направлениях тарифы в июле изменились сильнее всего',
            'link' => '#',
        ],
        [
            'images' => [
                'mobile' => 'slider/slide-2',
                'tablet' => 'slider/slide-2-desk',
            ],
            'title' => 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, contur',
            'subtitle' => 'fugiat nulla pariatur. Excepteur sint occaecat cupidatat asdasdasdasd',
            'link' => '#',
        ],
        [
            'images' => [
                'mobile' => 'slider/slide-2',
                'tablet' => 'slider/slide-2-desk',
            ],
            'title' => 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, contur',
            'subtitle' => 'fugiat nulla pariatur. Excepteur sint occaecat cupidatat asdasdasdasd',
            'link' => '#',
        ],
        [
            'images' => [
                'mobile' => 'slider/slide-2',
                'tablet' => 'slider/slide-2-desk',
            ],
            'title' => 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, contur',
            'subtitle' => 'fugiat nulla pariatur. Excepteur sint occaecat cupidatat asdasdasdasd',
            'link' => '#',
        ],
        [
            'images' => [
                'mobile' => 'slider/slide-2',
                'tablet' => 'slider/slide-2-desk',
            ],
            'title' => 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, contur',
            'subtitle' => 'fugiat nulla pariatur. Excepteur sint occaecat cupidatat asdasdasdasd',
            'link' => '#',
        ],
    ];
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
