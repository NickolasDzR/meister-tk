<?php

namespace App\Services;

use App\Models\Post;
use Throwable;

/**
 * Рассылает анонсы статей по площадкам. Берёт статьи, которые уже видны
 * на сайте, с включённым тумблером площадки, текстом и ещё не отправленные.
 * Запланированная статья уйдёт в ту же минуту, когда появится на сайте.
 */
class Announcer
{
    /** После стольких ошибок подряд площадка для статьи больше не пробуется. */
    public const MAX_ATTEMPTS = 3;

    /**
     * @return array<string, ?string> заголовок статьи => текст ошибки (null — отправлено)
     */
    public function run(AnnounceChannel $channel): array
    {
        $k = $channel->key();

        $posts = Post::published()
            ->where("{$k}_enabled", true)
            ->whereNotNull("{$k}_text")
            ->where("{$k}_text", '!=', '')
            ->whereNull("{$k}_posted_at")
            ->where("{$k}_attempts", '<', self::MAX_ATTEMPTS)
            ->orderBy('published_at')
            ->get();

        $results = [];

        foreach ($posts as $post) {
            try {
                $post->forceFill([
                    "{$k}_message_id" => $channel->publish($post),
                    "{$k}_posted_at" => now(),
                    "{$k}_error" => null,
                ])->save();

                $results[$post->title] = null;
            } catch (Throwable $e) {
                $post->forceFill([
                    "{$k}_attempts" => $post->{"{$k}_attempts"} + 1,
                    "{$k}_error" => $e->getMessage(),
                ])->save();

                $results[$post->title] = $e->getMessage();
            }
        }

        return $results;
    }
}
