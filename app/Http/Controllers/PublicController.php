<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Raffle;
use App\Models\Number;
use App\Models\Participant;
use App\Events\NumberAssigned;

class PublicController extends Controller
{
    // Página de inicio con todas las rifas
    public function index()
    {
        $raffles = Raffle::orderBy('created_at', 'desc')->get();
        return view('public.index', compact('raffles'));
    }

    // Ver detalles de una rifa
    public function show($id)
    {
        $raffle = Raffle::with('numbers', 'prizes')->findOrFail($id);
        return view('public.raffle', compact('raffle'));
    }

    // Vista del sorteo
    public function draw($id)
    {
        $raffle = Raffle::with('numbers', 'prizes')->findOrFail($id);
        return view('public.raffle', compact('raffle'));
    }

    // Obtener estadísticas actualizadas
    public function getStatistics($id)
    {
        $raffle = Raffle::with('numbers')->findOrFail($id);

        $statistics = [
            'disponibles' => $raffle->numbers->where('status', 'disponible')->count(),
            'vendidos' => $raffle->numbers->where('status', 'pagado')->count(),
            'reservados' => $raffle->numbers->where('status', 'reservado')->count(),
        ];

        return response()->json($statistics);
    }

    // Verificar participante existente
    public function checkParticipant(Request $request, $id)
    {
        $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        $email = $request->input('email');
        $phone = $request->input('phone');

        $participant = Participant::findExisting($email, $phone);

        if ($participant) {
            return response()->json([
                'exists' => true,
                'participant' => [
                    'name' => $participant->name,
                    'email' => $participant->email,
                    'phone' => $participant->phone,
                    'numbers_count' => $participant->numbers->count()
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }

    public function selectNumber(Request $request, $id)
    {
        $request->validate([
            'number_id' => 'required|exists:numbers,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255'
        ]);

        $number = Number::findOrFail($request->number_id);

        if ($number->status != 'disponible') {
            return response()->json(['error' => 'Número no disponible'], 400);
        }

        // Buscar participante existente por email o teléfono
        $existingParticipant = Participant::findExisting(
            $request->email,
            $request->phone
        );

        if ($existingParticipant) {
            // Usar participante existente
            $participant = $existingParticipant;

            // Actualizar información si es necesario
            $updated = false;
            if ($request->name && $participant->name !== $request->name) {
                $participant->name = $request->name;
                $updated = true;
            }
            if ($request->phone && $participant->phone !== $request->phone) {
                $participant->phone = $request->phone;
                $updated = true;
            }
            if ($request->email && $participant->email !== $request->email) {
                $participant->email = $request->email;
                $updated = true;
            }

            if ($updated) {
                $participant->save();
            }
        } else {
            // Crear nuevo participante
            $participant = Participant::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email
            ]);
        }

        // Asignar número
        $number->participant_id = $participant->id;
        $number->status = 'pagado';
        $number->save();

        // Evento de asignación de número
        event(new NumberAssigned($number));

        return response()->json([
            'success' => 'Número asignado correctamente',
            'participant_exists' => $existingParticipant ? true : false,
            'participant_name' => $participant->name
        ]);
    }
}
