<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // nullable — на случай системных/тестовых пользователей без компании.
            // constrained() создаёт внешний ключ на companies.id.
            // nullOnDelete() — если компанию удалят, пользователей не удаляем, просто отвязываем.
            $table->foreignId('company_id')
                ->nullable()
                ->after('id')
                ->constrained('companies')
                ->nullOnDelete();

            // admin — директор (создатель компании), employee — обычный сотрудник.
            // Роль внутри роли (логист/бухгалтер) вынесена в отдельное поле position,
            // чтобы не плодить enum-значения для прав доступа.
            $table->string('role')->default('employee')->after('company_id');
            $table->string('position')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn(['role', 'position']);
        });
    }
};
