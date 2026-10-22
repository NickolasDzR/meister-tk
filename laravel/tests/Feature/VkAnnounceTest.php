<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class VkAnnounceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.vk.token' => 'vk-secret',
            'services.vk.group_id' => '123456',
            'services.telegram.bot_token' => null,
        ]);
    }

    private function makePost(array $attributes = []): Post
    {
        static $n = 0;
        $n++;

        return Post::create($attributes + [
            'title' => "Статья $n",
            'slug' => "statya-$n",
            'excerpt' => 'Описание',
            'content' => [],
            'image' => 'posts/cover.jpg',
            'published' => true,
            'published_at' => now()->subMinute(),
            'vk_enabled' => true,
            'vk_text' => '**Важное** про грузы',
        ]);
    }

    public function test_posts_to_group_wall_once(): void
    {
        Http::fake(['api.vk.com/*' => Http::response(['response' => ['post_id' => 501]])]);
        $post = $this->makePost();
        $url = route('post', $post->slug);

        $this->artisan('posts:announce')->assertSuccessful();
        $this->artisan('posts:announce')->assertSuccessful();

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $r) => $r->url() === 'https://api.vk.com/method/wall.post'
            && $r['owner_id'] === -123456
            && $r['from_group'] === 1
            && $r['attachments'] === $url
            && $r['message'] === "Важное про грузы\n\nЧитать полностью: {$url}");

        $post->refresh();
        $this->assertSame(501, $post->vk_message_id);
        $this->assertSame('Отправлен', $post->announceStatus('vk'));
    }

    public function test_skips_when_toggle_off(): void
    {
        Http::fake();
        $this->makePost(['vk_enabled' => false]);

        $this->artisan('posts:announce')->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_records_vk_error_and_gives_up_after_three(): void
    {
        Http::fake(['*' => Http::response(['error' => ['error_code' => 15, 'error_msg' => 'Access denied']])]);
        $post = $this->makePost();

        foreach (range(1, 4) as $_) {
            $this->artisan('posts:announce');
        }

        $post->refresh();
        $this->assertSame('15: Access denied', $post->vk_error);
        $this->assertSame(3, $post->vk_attempts);
        $this->assertSame('Ошибка', $post->announceStatus('vk'));
        Http::assertSentCount(3);
    }

    public function test_connection_error_does_not_leak_token(): void
    {
        Http::fake(fn () => throw new ConnectionException('cURL error 28 access_token=vk-secret'));
        $post = $this->makePost();

        $this->artisan('posts:announce');

        $this->assertStringNotContainsString('vk-secret', $post->refresh()->vk_error);
    }
}
