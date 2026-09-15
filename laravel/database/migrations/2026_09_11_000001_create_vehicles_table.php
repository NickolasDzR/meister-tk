<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counterparty_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['tractor', 'single']);
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('plate')->nullable();
            $table->string('vin')->nullable();
            $table->string('sts_number')->nullable();

            $table->string('body_type')->nullable();
            $table->decimal('capacity_tons', 8, 2)->nullable();
            $table->decimal('volume_m3', 8, 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->string('ownership')->nullable();

            // Прицеп хранится прямо в машине, отдельной сущностью не выделен.
            $table->boolean('has_trailer')->default(false);
            $table->string('trailer_brand')->nullable();
            $table->string('trailer_plate')->nullable();
            $table->string('trailer_vin')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['counterparty_id', 'is_active']);
            $table->index('plate');
            $table->index('trailer_plate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
