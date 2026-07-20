<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Один пользователь (один email/аккаунт) может быть привязан
        // к нескольким компаниям — поэтому связь через отдельную
        // pivot-таблицу, а не через одиночный company_id в users.
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // admin — директор/владелец именно ЭТОЙ компании, employee — сотрудник.
            // Роль хранится на уровне пары (компания, пользователь), а не глобально
            // на пользователе: один и тот же человек может быть admin в одной
            // компании и employee в другой.
            $table->string('role')->default('employee');
            $table->string('position')->nullable();

            $table->timestamps();

            // Один пользователь не может быть привязан к одной и той же компании дважды.
            $table->unique(['company_id', 'user_id']);
        });

        // company_id/role/position на users больше не нужны — теперь это
        // атрибуты связи (pivot), а не самого пользователя.
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn(['role', 'position']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            $table->string('role')->default('employee')->after('company_id');
            $table->string('position')->nullable()->after('role');
        });

        Schema::dropIfExists('company_user');
    }
};
