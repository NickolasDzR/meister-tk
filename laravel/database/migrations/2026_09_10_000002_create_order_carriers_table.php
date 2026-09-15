<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_carriers', function (Blueprint $table) {
            $table->id();
            // Перевозчик один на заявку.
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('carrier_id')->constrained('counterparties')->restrictOnDelete();

            $table->string('contact_person')->nullable();
            $table->string('legal_entity')->nullable();
            $table->string('contract_number')->nullable();
            $table->date('contract_date')->nullable();

            $table->string('vehicle')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->string('trailer')->nullable();
            $table->string('trailer_plate')->nullable();

            $table->string('driver_1_name')->nullable();
            $table->string('driver_1_phone')->nullable();
            $table->string('driver_2_name')->nullable();
            $table->string('driver_2_phone')->nullable();

            $table->enum('rate_type', ['trip', 'hour', 'ton']);
            $table->decimal('rate', 15, 2);
            $table->string('rate_unit')->nullable();
            $table->integer('rate_quantity')->nullable();
            $table->decimal('rate_price', 15, 2)->nullable();

            $table->string('payment_form')->nullable();
            $table->text('payment_terms')->nullable();
            $table->integer('payment_days')->nullable();
            $table->string('payment_days_type')->nullable();

            $table->decimal('prepayment_amount', 15, 2)->nullable();
            $table->string('prepayment_form')->nullable();

            $table->boolean('documents_received')->default(false);
            $table->boolean('financial_documents_received')->default(false);
            $table->date('documents_received_date')->nullable();
            $table->date('payment_deadline')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('vehicle_plate');
            $table->index('payment_deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_carriers');
    }
};
