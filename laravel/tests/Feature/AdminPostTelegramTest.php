<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPostTelegramTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(array $attributes = []): Post
    {
        return Post::forceCreate($attributes + [
            'title' => 'Статья',
            'slug' => 'statya',
            'excerpt' => 'Описание',
            'content' => [],
            'image' => 'posts/cover.jpg',
            'published' => true,
            'telegram_enabled' => true,
            'telegram_text' => 'Анонс',
        ]);
    }

    public function test_edit_page_shows_telegram_section_and_status(): void
    {
        $post = $this->makePost(['telegram_attempts' => 3, 'telegram_error' => 'Bad Request: chat not found']);

        $this->actingAs(User::factory()->create())
            ->get("/admin/posts/{$post->id}/edit")
            ->assertOk()
            ->assertSee('Анонс в Telegram')
            ->assertSee('Не отправлен: Bad Request: chat not found')
            ->assertSee('Отправить в Telegram ещё раз');
    }

    public function test_list_shows_telegram_badge(): void
    {
        $this->makePost(['telegram_posted_at' => now()]);

        $this->actingAs(User::factory()->create())
            ->get('/admin/posts')
            ->assertOk()
            ->assertSee('Отправлен');
    }
}
