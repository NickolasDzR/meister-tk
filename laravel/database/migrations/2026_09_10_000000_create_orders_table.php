<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();

            $table->string('number');
            $table->date('date');
            $table->string('external_number')->nullable();

            $table->boolean('is_accepted')->default(false);
            $table->enum('status', ['created', 'accepted', 'cancelled', 'closed'])->default('created');
            $table->timestamp('status_changed_at')->nullable();

            $table->text('public_notes')->nullable();
            $table->boolean('is_note_important')->default(false);

            $table->text('special_conditions')->nullable();
            $table->text('additional_requirements')->nullable();

            $table->boolean('is_return_trip')->default(false);
            $table->boolean('is_insured')->default(false);

            // Пользователи не каскадятся: удаление менеджера не должно
            // утаскивать за собой все созданные им заявки.
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->constrained('users')->restrictOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Номер заявки генерируется внутри компании (001, 002, ...),
            // поэтому уникальность — по паре, иначе вторая компания
            // не сможет создать заявку с тем же номером.
            $table->unique(['company_id', 'number']);

            $table->index('number');
            $table->index('date');
            $table->index('status');
            $table->index('created_at');
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
