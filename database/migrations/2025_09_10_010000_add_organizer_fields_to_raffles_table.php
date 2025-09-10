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
        Schema::table('raffles', function (Blueprint $table) {
            // Organización
            $table->string('organizer_name')->nullable()->default(config('raffle.organizer_name'))->after('terms_html');
            $table->string('organizer_id')->nullable()->default(config('raffle.organizer_id'));
            $table->string('organizer_address')->nullable()->default(config('raffle.organizer_address'));
            $table->string('organizer_contact')->nullable()->default(config('raffle.organizer_contact'));
            $table->string('organizer_contact_email')->nullable()->default(config('raffle.organizer_contact_email'));

            // Plataforma
            $table->string('platform_name')->nullable()->default(config('raffle.platform_name'));
            $table->string('broadcast_platform')->nullable()->default(config('raffle.broadcast_platform'));
            $table->string('privacy_url')->nullable()->default(config('raffle.privacy_url'));

            // Otros
            $table->integer('claim_days')->nullable()->default(config('raffle.claim_days'));
            $table->string('jurisdiction_city')->nullable()->default(config('raffle.jurisdiction_city'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->dropColumn([
                'organizer_name',
                'organizer_id',
                'organizer_address',
                'organizer_contact',
                'organizer_contact_email',
                'platform_name',
                'broadcast_platform',
                'privacy_url',
                'claim_days',
                'jurisdiction_city',
            ]);
        });
    }
};


