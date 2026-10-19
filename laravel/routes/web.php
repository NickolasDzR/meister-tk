<?php

use App\Models\Post;
use App\Models\PromoSlide;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Слайдер на главной — витрина, а не архив: шесть свежих статей,
    // дальше седьмым слайдом ссылка на полный список.
    // Колонки выбираем поимённо: content со всем текстом статьи
    // главной не нужен, а весит он больше всего остального вместе взятого.
    $posts = Post::query()
        ->select('id', 'title', 'slug', 'excerpt', 'image', 'image_mobile', 'image_tablet', 'published_at')
        ->published()
        ->orderByDesc('published_at')
        ->limit(6)
        ->get();

    // Последний слайд слайдера — приглашение в список статей.
    // Редактируется в админке; выключен или не заведён — слайда просто нет.
    $promo = PromoSlide::active()->latest('updated_at')->first();

    return view('home', compact('posts', 'promo'));
})->name('home');

Route::get('/posts', function () {
    // По девять карточек на страницу. content со всем текстом статьи
    // в списке не нужен — выбираем только то, что показывает карточка.
    $posts = Post::query()
        ->select('id', 'title', 'slug', 'excerpt', 'image', 'image_mobile', 'image_tablet', 'published_at')
        ->published()
        ->orderByDesc('published_at')
        // Две статьи на одну минуту — порядок между ними задаёт id. Без этого
        // база вправе отдавать их по-разному, и при листании страниц статья
        // могла повториться на соседней странице или пропасть.
        ->orderByDesc('id')
        ->paginate(9);

    // Страницы за последней отдавали пустой список с кодом 200: человек видел
    // пустоту без объяснений, а поисковик — бесконечные адреса без содержимого.
    // Теперь это честный 404. Пустой список на первой странице (статей нет
    // вообще) под правило не попадает — это нормальное состояние сайта.
    abort_if($posts->currentPage() > $posts->lastPage() && $posts->total() > 0, 404);

    return view('posts', compact('posts'));
})->name('posts');

Route::get('/posts/{slug}', function (string $slug) {
    $post = Post::where('slug', $slug)
        ->published()
        ->firstOrFail();

    return view('post', compact('post'));
})->name('post');

// Подсказки городов для формы расчёта. Браузер спрашивает наш сервер,
// а сервер — Dadata: ключ остаётся здесь и не уезжает в скрипт, где его
// видит любой. Отдаём только то, что нужно форме: название и координаты.
// throttle — чтобы чужой скрипт не выел квоту Dadata (60 запросов в минуту
// с одного адреса; человек, набирающий город, делает их единицы).
Route::get('/address-suggest', function (Request $request) {
    $query = $request->validate([
        'query' => ['required', 'string', 'max:100'],
    ])['query'];

    try {
        $response = Http::withToken(config('services.dadata.api_key'), 'Token')
            ->timeout(10)
            ->post('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address', [
                'query' => $query,
                'count' => 20,
            ]);
    } catch (ConnectionException) {
        // Dadata не ответила. 502 — форма покажет «поиск не работает» и телефон.
        abort(502);
    }

    if ($response->failed()) {
        abort(502);
    }

    $suggestions = collect($response->json('suggestions', []))
        ->map(fn (array $item) => [
            'value' => $item['value'],
            'data' => [
                'geo_lat' => $item['data']['geo_lat'] ?? null,
                'geo_lon' => $item['data']['geo_lon'] ?? null,
            ],
        ]);

    return ['suggestions' => $suggestions];
})->middleware('throttle:60,1')->name('address-suggest');

// Каталог стилей — рабочий инструмент вёрстки, а не страница сайта.
// Маршрута попросту нет вне локальной машины, поэтому забыть выключить
// его негде: на сервере адрес отдаст 404.
if (app()->environment('local')) {
    Route::view('/styleguide', 'styleguide')->name('styleguide');
}
