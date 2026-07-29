<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counterparties', function (Blueprint $table) {
            // Раньше inn был уникален на всю таблицу — из-за этого две разные
            // компании не могли добавить себе одного и того же реального
            // контрагента (у него один ИНН, он не может быть другим).
            // Теперь уникальность — по паре (company_id, inn): один и тот же
            // контрагент не может быть добавлен дважды В ОДНОЙ компании,
            // но разные компании друг другу не мешают.
            $table->dropUnique(['inn']);
            $table->unique(['company_id', 'inn']);
        });
    }

    public function down(): void
    {
        Schema::table('counterparties', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'inn']);
            $table->unique('inn');
        });
    }
};
