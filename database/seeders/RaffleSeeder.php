<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Raffle;

class RaffleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Raffle::create([
            'name' => 'Gran Rifa de Navidad',
            'banner' => 'banners/navidad.jpg', // imagen en /public/storage/banners
            'description' => 'Participa y gana grandes premios. ¡Suerte a todos!',
            'draw_date' => '2025-08-15',
            'status' => 'en_venta',// programada, en_venta, finalizada
            'total_numbers' => 100,
            'theme_color' => '#dc3545'
        ]);
    }
}
