<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_carriers', function (Blueprint $table) {
            // Ссылки на справочники: «кто это сейчас».
            // Текстовые driver_1_name, vehicle_plate и прочие остаются рядом —
            // это слепок на момент рейса. Водитель сменит ВУ, машину продадут,
            // карточку отредактируют, а подписанная заявка-договор должна
            // остаться ровно такой, какой её подписали. Поэтому здесь set null,
            // а не cascade: запись из справочника исчезла — заявка цела.
            $table->foreignId('driver_1_id')->nullable()->after('trailer_plate')
                ->constrained('drivers')->nullOnDelete();
            $table->foreignId('driver_2_id')->nullable()->after('driver_1_id')
                ->constrained('drivers')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->after('driver_2_id')
                ->constrained('vehicles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_carriers', function (Blueprint $table) {
            $table->dropForeign(['driver_1_id']);
            $table->dropForeign(['driver_2_id']);
            $table->dropForeign(['vehicle_id']);
            $table->dropColumn(['driver_1_id', 'driver_2_id', 'vehicle_id']);
        });
    }
};
