<?php

namespace App\Models;

use App\Services\Announcer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'image_mobile',
        'image_tablet',
        'og_image',
        'published',
        'published_at',
        'telegram_enabled',
        'telegram_text',
        'vk_enabled',
        'vk_text',
    ];

    protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',
        'content' => 'array',
        'views' => 'integer',
        'telegram_enabled' => 'boolean',
        'telegram_posted_at' => 'datetime',
        'telegram_attempts' => 'integer',
        'vk_enabled' => 'boolean',
        'vk_posted_at' => 'datetime',
        'vk_attempts' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $post) {
            // Включили публикацию, а дату не тронули — значит публикуем сейчас.
            // Без этого статья уходила в ленту с пустой датой: не показывалась
            // под заголовком и падала в самый низ сортировки (MySQL ставит
            // NULL последним при ORDER BY ... DESC).
            if ($post->published && blank($post->published_at)) {
                $post->published_at = now();
            }
        });
    }

    /**
     * Статьи, которые реально видны на сайте: тумблер включён И время
     * публикации уже наступило. Один скоуп на все три места (слайдер,
     * список, страница статьи) — чтобы условие не разъехалось.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('published', true)
            ->where('published_at', '<=', now());
    }

    /**
     * Картинка для превью ссылки в мессенджерах: сначала специально
     * загруженный jpg, иначе обычная обложка. Null — если нет ни того, ни другого.
     */
    public function ogImageUrl(): ?string
    {
        $path = $this->og_image ?: $this->image;

        return $path ? Storage::disk('yandex')->url($path) : null;
    }

    /**
     * Опубликована, но время ещё не пришло — ждёт своего часа.
     */
    public function isScheduled(): bool
    {
        return $this->published && $this->published_at?->isFuture();
    }

    /**
     * Где сейчас анонс на площадке ('telegram', 'vk') — для админки.
     * Null, если отправка не включена или текста поста нет.
     */
    public function announceStatus(string $channel): ?string
    {
        return match (true) {
            $this->{"{$channel}_posted_at"} !== null => 'Отправлен',
            ! $this->{"{$channel}_enabled"} || blank($this->{"{$channel}_text"}) => null,
            $this->{"{$channel}_attempts"} >= Announcer::MAX_ATTEMPTS => 'Ошибка',
            default => 'Ждёт',
        };
    }
}
