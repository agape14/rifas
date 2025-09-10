<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->longText('terms_html')->nullable()->after('theme_color');
        });

        $default = <<<'HTML'
<p>El sorteo se realizará en esta plataforma en la fecha indicada. La hora exacta del sorteo se comunicará en el grupo de WhatsApp oficial.</p>
<p>Los números marcados como <strong>pagados</strong> participan en el sorteo. Los números <em>reservados</em> no garantizan participación hasta que sean pagados.</p>
<p>Al participar, aceptas las reglas de la rifa y el uso de tus datos para la gestión del sorteo.</p>
HTML;

        try {
            DB::table('raffles')->whereNull('terms_html')->update(['terms_html' => $default]);
        } catch (\Throwable $e) {
            // Silenciar errores si la tabla aún no existe en entornos frescos
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->dropColumn('terms_html');
        });
    }
};


