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
        Schema::table('number_status_audits', function (Blueprint $table) {
            $table->string('payment_evidence_path')->nullable()->after('notes');
            $table->string('payment_evidence_type')->nullable()->after('payment_evidence_path');
            $table->boolean('payment_confirmed')->default(false)->after('payment_evidence_type');
            $table->timestamp('payment_confirmed_at')->nullable()->after('payment_confirmed');
            $table->foreignId('payment_confirmed_by')->nullable()->constrained('users')->onDelete('set null')->after('payment_confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('number_status_audits', function (Blueprint $table) {
            $table->dropForeign(['payment_confirmed_by']);
            $table->dropColumn([
                'payment_evidence_path',
                'payment_evidence_type',
                'payment_confirmed',
                'payment_confirmed_at',
                'payment_confirmed_by'
            ]);
        });
    }
};
