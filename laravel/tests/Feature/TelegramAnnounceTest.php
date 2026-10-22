<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramAnnounceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.telegram.bot_token' => 'secret-token',
            'services.telegram.channel' => '@meister_test',
            'services.vk.token' => null,
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
            'telegram_enabled' => true,
            'telegram_text' => 'Анонс **важное** <тег> & ещё',
        ]);
    }

    public function test_sends_published_post_once(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true, 'result' => ['message_id' => 42]])]);
        $post = $this->makePost();

        $this->artisan('posts:announce')->assertSuccessful();
        $this->artisan('posts:announce')->assertSuccessful();

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/sendPhoto')
            && $r['chat_id'] === '@meister_test'
            && str_starts_with($r['caption'], 'Анонс <b>важное</b> &lt;тег&gt; &amp; ещё')
            && str_ends_with($r['caption'], '<a href="'.route('post', $post->slug).'">Читать полностью →</a>'));

        $post->refresh();
        $this->assertSame(42, $post->telegram_message_id);
        $this->assertNotNull($post->telegram_posted_at);
        $this->assertSame('Отправлен', $post->announceStatus('telegram'));
    }

    public function test_skips_disabled_drafts_scheduled_and_posts_without_text(): void
    {
        Http::fake();
        $disabled = $this->makePost(['telegram_enabled' => false]);
        $this->makePost(['published' => false]);
        $this->makePost(['published_at' => now()->addHour()]);
        $this->makePost(['telegram_text' => null]);
        $this->makePost(['telegram_text' => '']);

        $this->artisan('posts:announce')->assertSuccessful();

        Http::assertNothingSent();
        $this->assertNull($disabled->announceStatus('telegram'));
    }

    public function test_falls_back_to_text_when_photo_rejected(): void
    {
        Http::fake([
            '*/sendPhoto' => Http::response(['ok' => false, 'description' => 'Bad Request: wrong file'], 400),
            '*/sendMessage' => Http::response(['ok' => true, 'result' => ['message_id' => 7]]),
        ]);
        $post = $this->makePost();

        $this->artisan('posts:announce')->assertSuccessful();

        $this->assertSame(7, $post->refresh()->telegram_message_id);
    }

    public function test_gives_up_after_three_failures(): void
    {
        Http::fake(['*' => Http::response(['ok' => false, 'description' => 'Bad Request: chat not found'], 400)]);
        $post = $this->makePost();

        foreach (range(1, 5) as $_) {
            $this->artisan('posts:announce');
        }

        $post->refresh();
        $this->assertSame(3, $post->telegram_attempts);
        $this->assertSame('Bad Request: chat not found', $post->telegram_error);
        $this->assertNull($post->telegram_posted_at);
        $this->assertSame('Ошибка', $post->announceStatus('telegram'));
        // На каждую попытку: фото, затем текст.
        Http::assertSentCount(6);
    }

    public function test_connection_error_does_not_leak_token(): void
    {
        Http::fake(fn () => throw new ConnectionException('cURL error 28 for https://api.telegram.org/botsecret-token/sendPhoto'));
        $post = $this->makePost();

        $this->artisan('posts:announce');

        $this->assertStringNotContainsString('secret-token', $post->refresh()->telegram_error);
    }

    public function test_does_nothing_without_token(): void
    {
        config(['services.telegram.bot_token' => null]);
        Http::fake();
        $this->makePost();

        $this->artisan('posts:announce')->assertSuccessful();

        Http::assertNothingSent();
    }
}
