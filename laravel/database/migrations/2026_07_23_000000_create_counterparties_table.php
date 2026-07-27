<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counterparties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();

            $table->string('inn')->unique();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->enum('type', ['customer', 'carrier']);

            $table->string('city')->nullable();
            $table->string('legal_address')->nullable();
            $table->string('actual_address')->nullable();
            $table->boolean('address_matches')->default(true);

            $table->string('ogrn')->nullable();
            $table->string('kpp')->nullable();
            $table->string('ati_code')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('edo_identifier')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counterparties');
    }
};
