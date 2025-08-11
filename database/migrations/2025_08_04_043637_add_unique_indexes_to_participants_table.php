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
        // Agregar índices únicos solo si no existen
        if (! $this->indexExists('participants', 'participants_email_unique')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->unique('email');
            });
        }

        if (! $this->indexExists('participants', 'participants_phone_unique')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->unique('phone');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar índices únicos solo si existen
        if ($this->indexExists('participants', 'participants_email_unique')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->dropUnique('participants_email_unique');
            });
        }

        if ($this->indexExists('participants', 'participants_phone_unique')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->dropUnique('participants_phone_unique');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }
};
