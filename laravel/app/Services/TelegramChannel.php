<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Анонс статьи в Telegram-канал: обложка, текст из админки и ссылка на статью.
 */
class TelegramChannel
{
    public function isConfigured(): bool
    {
        return filled(config('services.telegram.bot_token'))
            && filled(config('services.telegram.channel'));
    }

    /**
     * Отправляет анонс и возвращает id сообщения в канале.
     *
     * @throws RuntimeException если Telegram не принял ни фото, ни текст
     */
    public function publish(Post $post): int
    {
        $text = $this->caption($post);
        $photo = $post->ogImageUrl();

        if ($photo) {
            $response = $this->call('sendPhoto', [
                'photo' => $photo,
                'caption' => $text,
            ]);

            if ($response['ok'] ?? false) {
                return $response['result']['message_id'];
            }

            // Фото не взялось (Telegram не всегда переваривает webp по ссылке) —
            // отправляем текстом: превью ссылки подтянет обложку из og:image.
        }

        $response = $this->call('sendMessage', ['text' => $text]);

        if (! ($response['ok'] ?? false)) {
            throw new RuntimeException($response['description'] ?? 'Telegram не ответил');
        }

        return $response['result']['message_id'];
    }

    /**
     * Текст из админки + ссылка «Читать полностью». В админке пишут обычным
     * текстом, **так** выделяется жирным — остальное экранируется, чтобы
     * случайный «<» или «&» не ломал разметку Telegram.
     */
    public function caption(Post $post): string
    {
        $text = htmlspecialchars(trim($post->telegram_text), ENT_NOQUOTES);
        $text = preg_replace('/\*\*(.+?)\*\*/su', '<b>$1</b>', $text);
        $url = htmlspecialchars(route('post', $post->slug), ENT_QUOTES);

        return $text."\n\n".'<a href="'.$url.'">Читать полностью →</a>';
    }

    private function call(string $method, array $params): array
    {
        $token = config('services.telegram.bot_token');

        try {
            return Http::timeout(20)
                ->asJson()
                ->post("https://api.telegram.org/bot{$token}/{$method}", $params + [
                    'chat_id' => config('services.telegram.channel'),
                    'parse_mode' => 'HTML',
                ])
                ->json() ?? [];
        } catch (ConnectionException) {
            // Своё сообщение вместо исходного: в исходном полный адрес запроса
            // вместе с токеном бота, а текст ошибки показывается в админке.
            throw new RuntimeException('Не удалось связаться с Telegram');
        }
    }
}
