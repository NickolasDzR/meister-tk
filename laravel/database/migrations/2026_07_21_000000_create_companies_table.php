<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // ИНН — уникальный идентификатор компании, по нему ищем в Дадате
            // и проверяем, что компания ещё не зарегистрирована в системе.
            $table->string('inn', 12)->unique();

            $table->string('name');
            $table->string('kpp', 9)->nullable();
            $table->string('ogrn', 15)->nullable();
            $table->string('address')->nullable();

            // Статус компании из Дадаты (ACTIVE / LIQUIDATED и т.д.) —
            // на будущее, чтобы не пускать в регистрацию ликвидированные компании.
            $table->string('dadata_status')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
