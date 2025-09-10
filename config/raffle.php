<?php

return [
    // Datos del organizador
    'organizer_name' => env('RAFFLE_ORGANIZER_NAME', 'Organizador'),
    'organizer_id' => env('RAFFLE_ORGANIZER_ID', '00000000'),
    'organizer_address' => env('RAFFLE_ORGANIZER_ADDRESS', 'Dirección del Organizador'),
    'organizer_contact' => env('RAFFLE_ORGANIZER_CONTACT', '+51 900 000 000'),
    'organizer_contact_email' => env('RAFFLE_ORGANIZER_CONTACT_EMAIL', 'organizador@example.com'),

    // Plataforma y políticas
    'platform_name' => env('RAFFLE_PLATFORM_NAME', config('app.name', 'Sistema de Rifas')),
    'broadcast_platform' => env('RAFFLE_BROADCAST_PLATFORM', 'YouTube / Facebook'),
    'privacy_url' => env('RAFFLE_PRIVACY_URL', '#'),

    // Otros
    'claim_days' => (int) env('RAFFLE_CLAIM_DAYS', 7),
    'jurisdiction_city' => env('RAFFLE_JURISDICTION_CITY', 'Lima'),
];


