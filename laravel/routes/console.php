<?php

use App\Services\Announcer;
use App\Services\TelegramChannel;
use App\Services\VkWall;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Анонсы опубликованных статей: Telegram-канал и стена ВК. Какие статьи
 * берутся — см. Announcer. Площадка без ключей в .env просто пропускается.
 */
Artisan::command('posts:announce', function (Announcer $announcer, TelegramChannel $telegram, VkWall $vk) {
    foreach ([$telegram, $vk] as $channel) {
        if (! $channel->isConfigured()) {
            $this->line("{$channel->name()}: ключи не заданы — пропускаю");

            continue;
        }

        foreach ($announcer->run($channel) as $title => $error) {
            $error === null
                ? $this->info("{$channel->name()}: отправлено — {$title}")
                : $this->error("{$channel->name()}: ошибка — {$title} — {$error}");
        }
    }
})->purpose('Отправить анонсы опубликованных статей в Telegram и ВК');

// На сервере schedule:run раз в минуту запускает cron (deploy/install-cron.sh).
// withoutOverlapping — чтобы медленный ответ площадки не привёл к двойной отправке.
Schedule::command('posts:announce')->everyMinute()->withoutOverlapping();
