<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Raffle;
use App\Models\Number;
use App\Models\Participant;
use App\Models\DrawResult;
use App\Events\NumberAssigned;

class PublicController extends Controller
{
    // Página de inicio con todas las rifas
    public function index()
    {
        $raffles = Raffle::withCount([
            'numbers as available_count' => function($q) { $q->where('status', 'disponible'); },
        ])->orderBy('created_at', 'desc')->get();
        return view('public.index', compact('raffles'));
    }

    // Ver detalles de una rifa
    public function show($id)
    {
        $raffle = Raffle::with('numbers.participant', 'prizes')->findOrFail($id);
        return view('public.raffle', compact('raffle'));
    }

    // Vista del sorteo
    public function draw($id)
    {
        $raffle = Raffle::with('numbers.participant', 'prizes')->findOrFail($id);

        $participants = $raffle->numbers
            ->where('status', 'pagado')
            ->map(function ($number) {
                return [
                    'id' => $number->id,
                    'number' => $number->number,
                    'participant_name' => $number->participant ? $number->participant->name : 'Sin nombre',
                    'participant_phone' => $number->participant ? $number->participant->phone : '',
                    'participant_email' => $number->participant ? $number->participant->email : '',
                ];
            })
            ->values();

        return view('public.draw', compact('raffle', 'participants'));
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

        // Finalizar rifa cuando se complete el sorteo
    public function finishRaffle($id)
    {
        try {
            $raffle = Raffle::findOrFail($id);
            $raffle->update(['status' => 'finalizada']);

            return response()->json([
                'success' => true,
                'message' => 'Rifa finalizada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar la rifa'
            ], 500);
        }
    }

    // Reservar número(s) (público/no autenticado)
    public function reserveNumber(Request $request, $id)
    {
        // Aceptar tanto number_id único como number_ids múltiples (CSV o array)
        // Normalizar teléfono a formato almacenado sin espacios
        if ($request->phone) {
            $request->merge(['phone' => \App\Models\Participant::normalizePeruPhone($request->phone)]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                // validar formato normalizado +519XXXXXXXX
                'regex:/^\+519\d{8}$/'
            ],
            'email' => 'nullable|email|max:255'
        ], [
            'phone.required' => 'El número de celular es obligatorio.',
            'phone.regex' => 'El celular debe tener el formato +519XXXXXXXX.'
        ]);

        $raffle = Raffle::findOrFail($id);

        // Normalizar ids
        $ids = $request->input('number_ids');
        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids)));
        }
        if (empty($ids)) {
            $singleId = $request->input('number_id');
            if ($singleId) {
                $ids = [$singleId];
            }
        }
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['error' => 'No se proporcionaron números válidos'], 422);
        }

        // Normalizar teléfono a formato almacenado sin espacios
        if ($request->phone) {
            $request->merge(['phone' => \App\Models\Participant::normalizePeruPhone($request->phone)]);
        }

        // Buscar/crear participante por email/phone
        $participant = null;
        if ($request->email || $request->phone) {
            $participant = Participant::where(function($q) use ($request) {
                if ($request->email) $q->where('email', $request->email);
                if ($request->phone) $q->orWhere('phone', $request->phone);
            })->first();
        }
        if (!$participant) {
            $participant = Participant::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email
            ]);
        } else {
            $participant->update([
                'name' => $request->name,
                'phone' => $request->phone ?: $participant->phone,
                'email' => $request->email ?: $participant->email,
            ]);
        }

        $processedIds = [];
        $failed = [];

        foreach ($ids as $numId) {
            $number = Number::find($numId);
            if (!$number) { $failed[] = ["id" => $numId, "error" => 'Número no encontrado']; continue; }
            if ($number->raffle_id != $raffle->id) { $failed[] = ["id" => $numId, "error" => 'No pertenece a esta rifa']; continue; }
            if ($number->status !== 'disponible') { $failed[] = ["id" => $numId, "error" => 'No disponible']; continue; }

            $number->participant_id = $participant->id;
            $number->status = 'reservado';
            $number->save();
            $processedIds[] = (int) $numId;
        }

        $msg = count($processedIds) === 1
            ? 'Número reservado correctamente'
            : 'Números reservados: '.count($processedIds);

        return response()->json([
            'success' => $msg,
            'participant_name' => $participant->name,
            'processed_ids' => $processedIds,
            'failed' => $failed,
        ], empty($processedIds) ? 422 : 200);
    }

    // Verificar participante existente
    public function checkParticipant(Request $request, $id)
    {
        $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        $raffle = Raffle::findOrFail($id);
        $email = $request->email;
        $phone = $request->phone;

        $participant = null;
        if ($email || $phone) {
            $query = Participant::whereHas('numbers', function($q) use ($raffle) {
                $q->where('raffle_id', $raffle->id);
            });

            if ($email) {
                $query->where('email', $email);
            }
            if ($phone) {
                $query->where('phone', $phone);
            }

            $participant = $query->first();
        }

        if ($participant) {
            $numbersCount = $participant->numbers()->where('raffle_id', $raffle->id)->count();
            return response()->json([
                'exists' => true,
                'participant' => [
                    'name' => $participant->name,
                    'phone' => $participant->phone,
                    'email' => $participant->email,
                    'numbers_count' => $numbersCount
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }

    // Asignar número(s) a participante como pagado (solo admin)
    public function selectNumber(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 403);
        }

        try {
            // Normalizar teléfono a formato almacenado sin espacios
            if ($request->phone) {
                $request->merge(['phone' => \App\Models\Participant::normalizePeruPhone($request->phone)]);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^\+519\d{8}$/'
                ],
                'email' => 'nullable|email|max:255'
            ], [
                'phone.required' => 'El número de celular es obligatorio.',
                'phone.regex' => 'El celular debe tener el formato +519XXXXXXXX.'
            ]);

            $raffle = Raffle::findOrFail($id);

            if ($raffle->status === 'finalizada') {
                return response()->json(['error' => 'Esta rifa ya ha sido finalizada y no se pueden realizar más inscripciones'], 400);
            }

            // Normalizar ids
            $ids = $request->input('number_ids');
            if (is_string($ids)) {
                $ids = array_filter(array_map('trim', explode(',', $ids)));
            }
            if (empty($ids)) {
                $singleId = $request->input('number_id');
                if ($singleId) {
                    $ids = [$singleId];
                }
            }
            if (empty($ids) || !is_array($ids)) {
                return response()->json(['error' => 'No se proporcionaron números válidos'], 422);
            }

            // Buscar participante existente o crear uno nuevo
            $participant = null;
            $participantExists = false;

            if ($request->email || $request->phone) {
                $query = Participant::where(function($q) use ($request) {
                    if ($request->email) {
                        $q->where('email', $request->email);
                    }
                    if ($request->phone) {
                        $q->orWhere('phone', $request->phone);
                    }
                });

                $participant = $query->first();
                if ($participant) {
                    $participantExists = true;
                    $participant->update([
                        'name' => $request->name,
                        'phone' => $request->phone ?: $participant->phone,
                        'email' => $request->email ?: $participant->email
                    ]);
                }
            }

            if (!$participant) {
                $participant = Participant::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email
                ]);
            }

            $processedIds = [];
            $failed = [];

            foreach ($ids as $numId) {
                $number = Number::find($numId);
                if (!$number) { $failed[] = ["id" => $numId, "error" => 'Número no encontrado']; continue; }
                if ($number->raffle_id != $raffle->id) { $failed[] = ["id" => $numId, "error" => 'No pertenece a esta rifa']; continue; }
                if ($number->status !== 'disponible') { $failed[] = ["id" => $numId, "error" => 'No disponible']; continue; }

                $number->participant_id = $participant->id;
                $number->status = 'pagado';
                $number->save();
                event(new NumberAssigned($number, $participant));
                $processedIds[] = (int) $numId;
            }

            $message = count($processedIds) === 1
                ? ($participantExists ? 'Número asignado al participante existente' : 'Participante registrado y número asignado')
                : 'Números marcados como pagados: '.count($processedIds);

            return response()->json([
                'success' => $message,
                'participant_name' => $participant->name,
                'participant_exists' => $participantExists,
                'processed_ids' => $processedIds,
                'failed' => $failed,
            ], empty($processedIds) ? 422 : 200);
        } catch (\Exception $e) {
            \Log::error('Error al asignar número: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    // Liberar número (solo para administradores)
    public function releaseNumber(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 403);
        }

        $request->validate([
            'number_id' => 'required|exists:numbers,id'
        ]);

        $raffle = Raffle::findOrFail($id);

        // Verificar que la rifa no esté finalizada
        if ($raffle->status === 'finalizada') {
            return response()->json(['error' => 'Esta rifa ya ha sido finalizada y no se pueden realizar más cambios'], 400);
        }

        $number = Number::findOrFail($request->number_id);

        // Verificar que el número pertenece a la rifa
        if ($number->raffle_id != $raffle->id) {
            return response()->json(['error' => 'El número no pertenece a esta rifa'], 400);
        }

        // Liberar el número
        $number->participant_id = null;
        $number->status = 'disponible';
        $number->save();

        return response()->json(['success' => 'Número liberado correctamente']);
    }

    // Método de prueba para debuggear
    public function testSelectNumber(Request $request, $id)
    {
        try {
            \Log::info('Test selectNumber called', [
                'request' => $request->all(),
                'raffle_id' => $id
            ]);

            $raffle = Raffle::findOrFail($id);
            $number = Number::where('raffle_id', $id)->where('status', 'disponible')->first();

            if (!$number) {
                return response()->json(['error' => 'No hay números disponibles'], 400);
            }

            // Crear participante de prueba
            $participant = Participant::create([
                'name' => 'Test User',
                'phone' => '123456789',
                'email' => 'test@example.com'
            ]);

            // Asignar número
            $number->participant_id = $participant->id;
            $number->status = 'pagado';
            $number->save();

            return response()->json([
                'success' => 'Número asignado correctamente',
                'number_id' => $number->id,
                'participant_name' => $participant->name
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en testSelectNumber: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Guardar ganador en la base de datos
    public function saveWinner(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            return response()->json(['error' => 'No tienes permisos para realizar esta acción'], 403);
        }

        try {
            $request->validate([
                'number' => 'required|integer',
                'participant_name' => 'required|string|max:255',
                'participant_phone' => 'nullable|string|max:20',
                'participant_email' => 'nullable|email|max:255',
                'prize_id' => 'required|exists:prizes,id'
            ]);

            $raffle = Raffle::findOrFail($id);
            $prize = $raffle->prizes()->findOrFail($request->prize_id);

            // Buscar o crear participante
            $participant = null;
            if ($request->participant_email || $request->participant_phone) {
                $participant = Participant::where(function($q) use ($request) {
                    if ($request->participant_email) $q->where('email', $request->participant_email);
                    if ($request->participant_phone) $q->orWhere('phone', $request->participant_phone);
                })->first();
            }

            if (!$participant) {
                $participant = Participant::create([
                    'name' => $request->participant_name,
                    'phone' => $request->participant_phone,
                    'email' => $request->participant_email
                ]);
            } else {
                // Actualizar datos del participante si es necesario
                $participant->update([
                    'name' => $request->participant_name,
                    'phone' => $request->participant_phone ?: $participant->phone,
                    'email' => $request->participant_email ?: $participant->email,
                ]);
            }

            // Crear resultado del sorteo
            $drawResult = DrawResult::create([
                'raffle_id' => $raffle->id,
                'prize_id' => $prize->id,
                'number' => $request->number,
                'participant_id' => $participant->id,
                'prize_image' => $prize->image,
                'drawn_at' => now(),
                'status' => 'winner'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ganador guardado exitosamente',
                'draw_result_id' => $drawResult->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al guardar ganador: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar ganador: ' . $e->getMessage()
            ], 500);
        }
    }
}
