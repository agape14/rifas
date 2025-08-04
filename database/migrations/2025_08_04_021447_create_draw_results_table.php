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
        Schema::create('draw_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->onDelete('cascade');
            $table->foreignId('prize_id')->constrained()->onDelete('cascade');
            $table->integer('number');
            $table->foreignId('participant_id')->nullable()->constrained()->onDelete('set null');
            $table->string('prize_image')->nullable(); // Copia de la imagen del premio
            $table->timestamp('drawn_at')->nullable();
            $table->string('status')->default('pending'); // pending, winner, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draw_results');
    }
};
