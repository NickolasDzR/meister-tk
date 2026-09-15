<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_waypoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['pickup', 'delivery']);
            $table->string('counterparty_name');
            $table->string('address');

            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('additional_info')->nullable();

            $table->date('date');
            $table->time('time');
            $table->integer('sequence');

            $table->timestamps();
            $table->softDeletes();

            // Основная выборка — точки одной заявки по порядку следования.
            $table->index(['order_id', 'sequence']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_waypoints');
    }
};
