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
        Schema::create('prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raffle_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('description')->nullable();
            $table->integer('order')->default(1); // Orden en que se entregará
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prizes');
    }
};
