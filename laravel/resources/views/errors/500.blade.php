@php
    $nav = [
        ['link' => route('posts'), 'text' => 'Статьи'],
    ];
@endphp

@extends('layouts.app')

@section('page', 'error')

@section('title', 'На сайте что-то сломалось — ООО «Мейстер»')

@section('content')
    {{-- Шапка, поле и подвал — ровно в высоту экрана: поле занимает остаток. --}}
    <div class="error-page">
    @include('modules.header.header', ['nav' => $nav, 'modifiers' => 'header_still-logo'])
    <main class="error-page__main">
        @include('modules.error.error', [
            'code' => 500,
            'title' => 'На сайте что-то сломалось',
            'text' => 'Мы уже видим ошибку и чиним её. Попробуйте обновить страницу через пару минут или вернитесь на главную.',
        ])
    </main>
    @include('modules.footer.footer', ['bordered' => false])
    </div>
@endsection
