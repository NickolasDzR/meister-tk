<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counterparty_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counterparty_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');

            $table->foreignId('uploaded_by')->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counterparty_files');
    }
};
