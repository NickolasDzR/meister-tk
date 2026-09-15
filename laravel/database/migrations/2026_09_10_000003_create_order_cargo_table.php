<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_cargo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->string('name')->nullable();
            $table->string('weight')->nullable();
            $table->string('volume')->nullable();
            $table->string('dimensions')->nullable();

            $table->string('packaging_type')->nullable();
            $table->integer('packaging_quantity')->nullable();
            $table->decimal('cost', 15, 2)->nullable();

            $table->enum('loading_type', ['top', 'side', 'rear'])->nullable();
            $table->enum('unloading_type', ['top', 'side', 'rear'])->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_cargo');
    }
};
