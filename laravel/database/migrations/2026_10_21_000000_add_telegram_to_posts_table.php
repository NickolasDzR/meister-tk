<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Пост в Telegram-канал, который уходит сам, когда статья появилась на сайте.
     * Отправляется, только если в админке включён тумблер и есть текст.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('telegram_enabled')->default(false)->after('views');
            $table->text('telegram_text')->nullable()->after('telegram_enabled');
            $table->timestamp('telegram_posted_at')->nullable()->after('telegram_text');
            $table->unsignedBigInteger('telegram_message_id')->nullable()->after('telegram_posted_at');
            // Неудачные попытки: после трёх подряд планировщик больше не пробует,
            // чтобы не долбить Telegram каждую минуту. Сбрасывается кнопкой в админке.
            $table->unsignedTinyInteger('telegram_attempts')->default(0)->after('telegram_message_id');
            $table->text('telegram_error')->nullable()->after('telegram_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_enabled',
                'telegram_text',
                'telegram_posted_at',
                'telegram_message_id',
                'telegram_attempts',
                'telegram_error',
            ]);
        });
    }
};
