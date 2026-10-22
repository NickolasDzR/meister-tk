<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'dadata' => [
        'api_key' => env('DADATA_API_KEY'),
        'secret_key' => env('DADATA_SECRET_KEY'),
    ],

    // Яндекс.Карты — маршрут в форме расчёта доставки. Ключ уходит
    // в браузер (так устроены Карты), поэтому он ограничен доменами
    // в кабинете Яндекса.
    'yandex_maps' => [
        'api_key' => env('YMAP_API_KEY'),
    ],

    // Бот, который публикует анонсы статей в канал. Бот должен быть
    // администратором канала с правом публикации. Пустой токен — отправка
    // выключена, статьи просто ждут.
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        // @имя_канала или числовой id вида -100…
        'channel' => env('TELEGRAM_CHANNEL'),
    ],

    // Ключ сообщества ВК с правом на стену (Управление → Работа с API).
    // Пустой — отправка выключена.
    'vk' => [
        'token' => env('VK_TOKEN'),
        // Число из адреса club123456 — без минуса
        'group_id' => env('VK_GROUP_ID'),
    ],

];
