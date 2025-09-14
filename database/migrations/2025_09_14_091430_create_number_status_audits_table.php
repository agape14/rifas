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
        Schema::create('number_status_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('number_id')->constrained()->onDelete('cascade');
            $table->foreignId('raffle_id')->constrained()->onDelete('cascade');
            $table->foreignId('participant_id')->nullable()->constrained()->onDelete('set null');
            $table->string('old_status')->nullable(); // Estado anterior
            $table->string('new_status'); // Nuevo estado
            $table->string('action_type'); // 'individual', 'bulk_mark_paid', 'bulk_release'
            $table->decimal('amount', 10, 2)->nullable(); // Monto del cobro (si aplica)
            $table->text('notes')->nullable(); // Notas adicionales
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade'); // Usuario que hizo el cambio
            $table->json('bulk_data')->nullable(); // Datos adicionales para acciones masivas
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['raffle_id', 'created_at']);
            $table->index(['number_id', 'created_at']);
            $table->index(['action_type', 'created_at']);
            $table->index(['new_status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('number_status_audits');
    }
};
