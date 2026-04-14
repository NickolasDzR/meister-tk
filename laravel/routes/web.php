<?php

use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
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