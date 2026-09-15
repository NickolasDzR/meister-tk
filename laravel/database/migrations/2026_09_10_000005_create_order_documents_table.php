<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // Документ привязан к строке заказчика в заявке (order_customers),
            // а не к контрагенту из справочника.
            $table->foreignId('customer_id')->nullable()->constrained('order_customers')->cascadeOnDelete();

            $table->enum('party', ['customer', 'carrier']);
            $table->string('type');
            $table->string('number')->nullable();
            $table->date('date')->nullable();
            $table->date('deadline')->nullable();

            $table->enum('status', ['draft', 'sent', 'received', 'signed', 'paid', 'overdue'])->default('draft');

            $table->string('postal_tracking')->nullable();
            $table->date('postal_date')->nullable();
            $table->date('return_date')->nullable();

            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('number');
            $table->index('date');
            $table->index('status');
            $table->index('deadline');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_documents');
    }
};
