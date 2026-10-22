<?php

namespace App\Services;

use App\Models\Post;

/**
 * Куда уходит анонс статьи: Telegram-канал, стена ВК.
 *
 * key() — префикс полей статьи: {key}_enabled, {key}_text, {key}_posted_at,
 * {key}_message_id, {key}_attempts, {key}_error.
 */
interface AnnounceChannel
{
    public function key(): string;

    /** Название для логов и админки. */
    public function name(): string;

    public function isConfigured(): bool;

    /**
     * Отправляет анонс и возвращает id записи на площадке.
     *
     * @throws \RuntimeException с текстом ошибки, который увидят в админке
     */
    public function publish(Post $post): int;

    /** Готовый текст со ссылкой — для ручной публикации, если автоотправка выключена. */
    public function manualText(Post $post): string;
}
