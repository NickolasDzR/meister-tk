<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Список статей и слайдер на главной делают один и тот же запрос:
            // WHERE published = 1 ORDER BY published_at DESC.
            // Без индекса MySQL читал таблицу целиком и сортировал результат
            // в памяти (EXPLAIN: type=ALL, Using filesort) на каждый заход.
            // Порядок колонок важен: сначала та, по которой фильтруем,
            // затем та, по которой сортируем.
            $table->index(['published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['published', 'published_at']);
        });
    }
};
