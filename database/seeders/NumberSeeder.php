<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Number;
use App\Models\Raffle;
class NumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $raffle = Raffle::first();

        for ($i = 1; $i <= $raffle->total_numbers; $i++) {
            Number::create([
                'raffle_id' => $raffle->id,
                'number' => $i,
                'status' => 'disponible', // disponible, reservado, pagado
            ]);
        }
    }
}
