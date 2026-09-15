<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_rate_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // Заполняется только когда ставка менялась для заказчика.
            $table->foreignId('customer_id')->nullable()->constrained('order_customers')->cascadeOnDelete();

            $table->enum('party', ['customer', 'carrier']);

            $table->decimal('old_rate', 15, 2);
            $table->decimal('new_rate', 15, 2);
            $table->text('reason')->nullable();

            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();

            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_rate_history');
    }
};
