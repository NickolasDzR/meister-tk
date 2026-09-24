<?php

use App\Models\Post;
use App\Models\PromoSlide;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Слайдер на главной — витрина, а не архив: шесть свежих статей,
    // дальше седьмым слайдом ссылка на полный список.
    // Колонки выбираем поимённо: content со всем текстом статьи
    // главной не нужен, а весит он больше всего остального вместе взятого.
    $posts = Post::query()
        ->select('id', 'title', 'slug', 'excerpt', 'image', 'image_mobile', 'image_tablet', 'published_at')
        ->where('published', true)
        ->orderByDesc('published_at')
        ->limit(6)
        ->get();

    // Последний слайд слайдера — приглашение в список статей.
    // Редактируется в админке; выключен или не заведён — слайда просто нет.
    $promo = PromoSlide::active()->latest('updated_at')->first();

    return view('home', compact('posts', 'promo'));
});

Route::get('/posts', function () {
    $posts = Post::where('published', true)
        ->orderByDesc('published_at')
        ->get();

    return view('posts', compact('posts'));
})->name('posts');

Route::get('/posts/{slug}', function (string $slug) {
    $post = Post::where('slug', $slug)
        ->where('published', true)
        ->firstOrFail();

    return view('post', compact('post'));
})->name('post');

require __DIR__.'/auth.php';