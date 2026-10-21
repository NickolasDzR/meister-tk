<?php

use App\Models\Post;
use App\Services\TelegramChannel;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Анонсы статей в Telegram-канал. Берёт статьи, которые уже видны на сайте,
 * с включённым тумблером «Отправить анонс», текстом поста и ещё не
 * отправленные. Запланированная статья уйдёт в канал в ту же минуту,
 * когда появится на сайте.
 */
Artisan::command('posts:telegram', function (TelegramChannel $telegram) {
    if (! $telegram->isConfigured()) {
        $this->warn('TELEGRAM_BOT_TOKEN или TELEGRAM_CHANNEL не заданы — пропускаю');

        return;
    }

    $posts = Post::published()
        ->where('telegram_enabled', true)
        ->whereNotNull('telegram_text')
        ->where('telegram_text', '!=', '')
        ->whereNull('telegram_posted_at')
        ->where('telegram_attempts', '<', 3)
        ->orderBy('published_at')
        ->get();

    foreach ($posts as $post) {
        try {
            $post->forceFill([
                'telegram_message_id' => $telegram->publish($post),
                'telegram_posted_at' => now(),
                'telegram_error' => null,
            ])->save();

            $this->info("Отправлено: {$post->title}");
        } catch (Throwable $e) {
            $post->forceFill([
                'telegram_attempts' => $post->telegram_attempts + 1,
                'telegram_error' => $e->getMessage(),
            ])->save();

            $this->error("Ошибка: {$post->title} — {$e->getMessage()}");
        }
    }
})->purpose('Отправить анонсы опубликованных статей в Telegram-канал');

// На сервере schedule:run раз в минуту запускает cron (deploy/install-cron.sh).
// withoutOverlapping — чтобы медленный ответ Telegram не привёл к двойной отправке.
Schedule::command('posts:telegram')->everyMinute()->withoutOverlapping();
