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

    public function test_edit_page_shows_error_and_retry_when_auto_sending(): void
    {
        config(['services.telegram.bot_token' => 'token', 'services.telegram.channel' => '@test']);
        $post = $this->makePost(['telegram_attempts' => 3, 'telegram_error' => 'Bad Request: chat not found']);

        $this->actingAs(User::factory()->create())
            ->get("/admin/posts/{$post->id}/edit")
            ->assertOk()
            ->assertSee('Анонс: Telegram')
            ->assertSee('Отправить анонс: Telegram')
            ->assertSee('Не отправлен: Bad Request: chat not found')
            ->assertSee('Отправить ещё раз: Telegram')
            ->assertDontSee('Отметить отправленным');
    }

    public function test_manual_mode_without_keys(): void
    {
        config(['services.telegram.bot_token' => null, 'services.vk.token' => null]);
        $post = $this->makePost(['telegram_enabled' => false]);

        $this->actingAs(User::factory()->create())
            ->get("/admin/posts/{$post->id}/edit")
            ->assertOk()
            ->assertSee('Автоотправка выключена')
            ->assertDontSee('Отправить анонс: Telegram')
            ->assertSee('Скопировать анонс')
            ->assertSee('Отметить отправленным');
    }

    public function test_list_shows_status_badges(): void
    {
        $this->makePost(['telegram_posted_at' => now()]);

        $this->actingAs(User::factory()->create())
            ->get('/admin/posts')
            ->assertOk()
            ->assertSee('Отправлен')
            ->assertSee('ВК');
    }
}
