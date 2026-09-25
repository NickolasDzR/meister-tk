<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Отдельная картинка для превью ссылки в мессенджерах.
            // Основные обложки — webp ради скорости загрузки сайта, а телеграм
            // и вк webp в превью показывают ненадёжно. Поэтому сюда кладём jpg.
            // Пусто — берётся обычная обложка.
            $table->string('og_image')->nullable()->after('image_tablet');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('og_image');
        });
    }
};
