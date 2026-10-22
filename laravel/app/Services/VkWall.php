<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Анонс статьи на стене сообщества ВК: текст и ссылка на статью во вложении.
 *
 * Фото не загружаем: загрузка картинок на стену требует ключ пользователя,
 * а пост хватает ключа сообщества. Карточку ссылки с обложкой ВК собирает
 * сам из og:image статьи.
 */
class VkWall implements AnnounceChannel
{
    private const API_VERSION = '5.199';

    public function key(): string
    {
        return 'vk';
    }

    public function name(): string
    {
        return 'ВКонтакте';
    }

    public function isConfigured(): bool
    {
        return filled(config('services.vk.token'))
            && filled(config('services.vk.group_id'));
    }

    public function publish(Post $post): int
    {
        $url = route('post', $post->slug);

        try {
            $response = Http::timeout(20)
                ->asForm()
                ->post('https://api.vk.com/method/wall.post', [
                    'owner_id' => -abs((int) config('services.vk.group_id')),
                    'from_group' => 1,
                    'message' => $this->message($post),
                    'attachments' => $url,
                    'access_token' => config('services.vk.token'),
                    'v' => self::API_VERSION,
                ])
                ->json() ?? [];
        } catch (ConnectionException) {
            // Своё сообщение: текст ошибки показывается в админке.
            throw new RuntimeException('Не удалось связаться с ВКонтакте');
        }

        if (isset($response['error'])) {
            throw new RuntimeException(
                ($response['error']['error_code'] ?? '?').': '.($response['error']['error_msg'] ?? 'ВКонтакте вернул ошибку')
            );
        }

        return $response['response']['post_id']
            ?? throw new RuntimeException('ВКонтакте не вернул номер записи');
    }

    /**
     * ВК не умеет жирный в постах — **звёздочки** убираем, если текст
     * скопировали из анонса для Telegram.
     */
    public function message(Post $post): string
    {
        $text = preg_replace('/\*\*(.+?)\*\*/su', '$1', trim($post->vk_text));

        return $text."\n\nЧитать полностью: ".route('post', $post->slug);
    }

    public function manualText(Post $post): string
    {
        return $this->message($post);
    }
}
