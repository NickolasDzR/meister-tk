@php
    $nav = [
        ['link' => route('posts'), 'text' => 'Статьи'],
    ];
@endphp

@extends('layouts.app')

@section('page', 'error')

@section('title', 'На сайте что-то сломалось — ООО «Мейстер»')

@section('content')
    @include('modules.header.header', ['nav' => $nav, 'modifiers' => 'header_bordered header_still-logo'])
    <main>
        @include('modules.error.error', [
            'code' => 500,
            'title' => 'На сайте что-то сломалось',
            'text' => 'Мы уже видим ошибку и чиним её. Попробуйте обновить страницу через пару минут или вернитесь на главную.',
        ])
    </main>
    @include('modules.footer.footer')
@endsection
