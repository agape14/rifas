<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prize;
use App\Models\Raffle;

class PrizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $raffle = Raffle::first();

        Prize::create([
            'raffle_id' => $raffle->id,
            'name' => 'Televisor 50"',
            'image' => 'prizes/tv50.jpg',
            'description' => 'Smart TV UHD 4K',
            'order' => 1
        ]);

        Prize::create([
            'raffle_id' => $raffle->id,
            'name' => 'Bicicleta de Montaña',
            'image' => 'prizes/bici.jpg',
            'description' => 'Bicicleta todo terreno',
            'order' => 2
        ]);

        Prize::create([
            'raffle_id' => $raffle->id,
            'name' => 'Canasta Navideña',
            'image' => 'prizes/canasta.jpg',
            'description' => 'Productos navideños variados',
            'order' => 3
        ]);
    }
}
