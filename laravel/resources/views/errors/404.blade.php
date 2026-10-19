@php
    $nav = [
        ['link' => route('posts'), 'text' => 'Статьи'],
    ];
@endphp

@extends('layouts.app')

@section('page', 'error')

@section('title', 'Такой страницы нет — ООО «Мейстер»')

@section('content')
    @include('modules.header.header', ['nav' => $nav, 'modifiers' => 'header_bordered header_still-logo'])
    <main>
        @include('modules.error.error', [
            'code' => 404,
            'title' => 'Такой страницы нет',
            'text' => 'Возможно, ссылка устарела или в адресе опечатка. Начните с главной или загляните в статьи.',
        ])
    </main>
    @include('modules.footer.footer')
@endsection
