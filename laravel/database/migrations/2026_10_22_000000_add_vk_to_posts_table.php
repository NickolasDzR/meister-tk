<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Анонс на стене сообщества ВК — те же поля, что у Telegram.
     * vk_message_id — номер записи на стене (post_id).
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('vk_enabled')->default(false)->after('telegram_error');
            $table->text('vk_text')->nullable()->after('vk_enabled');
            $table->timestamp('vk_posted_at')->nullable()->after('vk_text');
            $table->unsignedBigInteger('vk_message_id')->nullable()->after('vk_posted_at');
            $table->unsignedTinyInteger('vk_attempts')->default(0)->after('vk_message_id');
            $table->text('vk_error')->nullable()->after('vk_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'vk_enabled',
                'vk_text',
                'vk_posted_at',
                'vk_message_id',
                'vk_attempts',
                'vk_error',
            ]);
        });
    }
};
