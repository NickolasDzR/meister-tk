<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            // Водитель принадлежит перевозчику: удалили перевозчика — ушли и его водители.
            $table->foreignId('counterparty_id')->constrained()->cascadeOnDelete();

            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('inn')->nullable();

            $table->string('license_series')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_issued_at')->nullable();
            $table->date('license_expires_at')->nullable();
            $table->string('license_categories')->nullable();

            $table->string('passport_series')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('passport_issued_by')->nullable();
            $table->date('passport_issued_at')->nullable();
            $table->string('passport_department_code')->nullable();
            $table->string('registration_address')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Выборка «активные водители этого перевозчика» — основной запрос формы.
            $table->index(['counterparty_id', 'is_active']);
            $table->index('last_name');
            $table->index('license_expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
