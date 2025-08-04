<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('banner')->nullable(); // Imagen principal
            $table->text('description')->nullable(); // Descripción larga
            $table->date('draw_date')->nullable(); // Fecha del sorteo
            $table->enum('status', ['programada', 'en_venta', 'finalizada'])->default('programada');
            $table->integer('total_numbers')->default(100);
            $table->string('theme_color')->nullable(); // Personalizar vista
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raffles');
    }
};
