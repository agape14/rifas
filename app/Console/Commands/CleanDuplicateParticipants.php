<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;

class CleanDuplicateParticipants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'participants:clean-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean duplicate participants by email and phone';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning duplicate participants...');

        // Limpiar duplicados por email
        $this->cleanDuplicatesByField('email');

        // Limpiar duplicados por teléfono
        $this->cleanDuplicatesByField('phone');

        $this->info('Duplicate participants cleaned successfully!');
    }

    private function cleanDuplicatesByField($field)
    {
        $this->info("Cleaning duplicates by {$field}...");

        // Obtener duplicados
        $duplicates = DB::table('participants')
            ->select($field, DB::raw('COUNT(*) as count'))
            ->whereNotNull($field)
            ->where($field, '!=', '')
            ->groupBy($field)
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            $this->info("Found {$duplicate->count} duplicates for {$field}: {$duplicate->$field}");

            // Obtener todos los participantes con este email/teléfono
            $participants = Participant::where($field, $duplicate->$field)
                ->orderBy('created_at')
                ->get();

            // Mantener el primero (más antiguo) y eliminar los demás
            $keepParticipant = $participants->first();
            $deleteParticipants = $participants->skip(1);

            foreach ($deleteParticipants as $participant) {
                // Transferir números al participante que se mantiene
                $numbers = $participant->numbers;
                foreach ($numbers as $number) {
                    $number->participant_id = $keepParticipant->id;
                    $number->save();
                }

                // Eliminar el participante duplicado
                $participant->delete();
                $this->info("Deleted duplicate participant: {$participant->name} (ID: {$participant->id})");
            }
        }
    }
}
