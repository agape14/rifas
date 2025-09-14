<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Number;
use App\Models\NumberStatusAudit;
use App\Models\Raffle;

class NumberController extends Controller
{
    /**
     * Registrar cambio de estado en auditoría
     */
    private function logStatusChange(Number $number, string $oldStatus, string $newStatus, string $actionType, array $additionalData = [])
    {
        NumberStatusAudit::create([
            'number_id' => $number->id,
            'raffle_id' => $number->raffle_id,
            'participant_id' => $number->participant_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'action_type' => $actionType,
            'amount' => $additionalData['amount'] ?? null,
            'notes' => $additionalData['notes'] ?? null,
            'changed_by' => auth()->id(),
            'bulk_data' => $additionalData['bulk_data'] ?? null
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $raffleId = $request->query('raffle_id');
        $searchQuery = trim((string) $request->query('q', ''));

        $query = Number::with(['raffle', 'participant'])->orderBy('number');
        $currentRaffle = null;

        if ($raffleId) {
            $query->where('raffle_id', $raffleId);
            $currentRaffle = Raffle::find($raffleId);
        }

        if ($searchQuery !== '') {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('number', 'like', "%$searchQuery%")
                  ->orWhere('status', 'like', "%$searchQuery%")
                  ->orWhereHas('participant', function ($p) use ($searchQuery) {
                      $p->where('name', 'like', "%$searchQuery%")
                        ->orWhere('phone', 'like', "%$searchQuery%")
                        ->orWhere('email', 'like', "%$searchQuery%");
                  })
                  ->orWhereHas('raffle', function ($r) use ($searchQuery) {
                      $r->where('name', 'like', "%$searchQuery%");
                  });
            });
        }

        $numbers = $query->paginate(20)->appends($request->query());

        return view('admin.numbers.index', compact('numbers', 'currentRaffle', 'searchQuery'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $raffles = Raffle::all();
        return view('admin.numbers.create', compact('raffles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'raffle_id' => 'required|exists:raffles,id',
            'number' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
        ]);

        // Verificar que el número no exista ya para esta rifa
        $existingNumber = Number::where('raffle_id', $request->raffle_id)
            ->where('number', $request->number)
            ->first();

        if ($existingNumber) {
            return back()->withErrors(['number' => 'Este número ya existe para esta rifa']);
        }

        Number::create($request->all());

        return redirect()->route('numbers.index')->with('success', 'Número creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $number = Number::with(['raffle', 'participant'])->findOrFail($id);
        return view('admin.numbers.show', compact('number'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $number = Number::findOrFail($id);
        $raffles = Raffle::all();
        return view('admin.numbers.edit', compact('number', 'raffles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $number = Number::findOrFail($id);

        $request->validate([
            'raffle_id' => 'required|exists:raffles,id',
            'number' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:disponible,reservado,pagado',
        ]);

        // Verificar que el número no exista ya para esta rifa (excluyendo el actual)
        $existingNumber = Number::where('raffle_id', $request->raffle_id)
            ->where('number', $request->number)
            ->where('id', '!=', $id)
            ->first();

        if ($existingNumber) {
            return back()->withErrors(['number' => 'Este número ya existe para esta rifa']);
        }

        $number->update($request->all());

        return redirect()->route('numbers.index')->with('success', 'Número actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $number = Number::findOrFail($id);
        $number->delete();

        return redirect()->route('numbers.index')->with('success', 'Número eliminado correctamente');
    }

    public function markPaid(Request $request, string $id)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        $number = Number::findOrFail($id);
        if ($number->status !== 'reservado') {
            return back()->withErrors(['status' => 'Solo se puede marcar como pagado un número reservado']);
        }

        $oldStatus = $number->status;
        $number->status = 'pagado';
        $number->save();

        // Registrar en auditoría
        $this->logStatusChange($number, $oldStatus, 'pagado', 'individual', [
            'amount' => $request->amount,
            'notes' => $request->notes
        ]);

        $message = 'Número marcado como pagado correctamente';
        if ($request->amount) {
            $message .= ' con monto de S/.' . number_format($request->amount, 2);
        }

        return back()->with('success', $message);
    }

    public function release(string $id)
    {
        $number = Number::findOrFail($id);
        $oldStatus = $number->status;

        $number->participant_id = null;
        $number->status = 'disponible';
        $number->save();

        // Registrar en auditoría
        $this->logStatusChange($number, $oldStatus, 'disponible', 'individual');

        return back()->with('success', 'Número liberado correctamente');
    }

    public function bulkMarkPaid(Request $request)
    {
        $request->validate([
            'number_ids' => 'required|string',
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $numberIds = json_decode($request->number_ids, true);

            if (!is_array($numberIds) || empty($numberIds)) {
                return back()->withErrors(['number_ids' => 'No se proporcionaron números válidos']);
            }

            // Verificar que todos los números existen y están en estado reservado
            $numbers = Number::whereIn('id', $numberIds)
                ->where('status', 'reservado')
                ->get();

            if ($numbers->count() !== count($numberIds)) {
                return back()->withErrors(['number_ids' => 'Algunos números no están disponibles o no están en estado reservado']);
            }

            // Registrar auditoría para cada número antes del cambio
            foreach ($numbers as $number) {
                $this->logStatusChange($number, $number->status, 'pagado', 'bulk_mark_paid', [
                    'amount' => $request->amount,
                    'notes' => $request->notes,
                    'bulk_data' => [
                        'total_numbers' => count($numberIds),
                        'bulk_action_date' => now()->toISOString()
                    ]
                ]);
            }

            // Marcar como pagados
            $updatedCount = Number::whereIn('id', $numberIds)
                ->where('status', 'reservado')
                ->update(['status' => 'pagado']);

            $message = $updatedCount === 1
                ? '1 número marcado como pagado correctamente'
                : "{$updatedCount} números marcados como pagados correctamente";

            if ($request->amount) {
                $message .= ' con monto de S/.' . number_format($request->amount, 2);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Error en bulkMarkPaid: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
        }
    }

    public function bulkRelease(Request $request)
    {
        $request->validate([
            'number_ids' => 'required|string'
        ]);

        try {
            $numberIds = json_decode($request->number_ids, true);

            if (!is_array($numberIds) || empty($numberIds)) {
                return back()->withErrors(['number_ids' => 'No se proporcionaron números válidos']);
            }

            // Verificar que todos los números existen y no están disponibles
            $numbers = Number::whereIn('id', $numberIds)
                ->whereIn('status', ['reservado', 'pagado'])
                ->get();

            if ($numbers->count() !== count($numberIds)) {
                return back()->withErrors(['number_ids' => 'Algunos números no están disponibles para liberar']);
            }

            // Registrar auditoría para cada número antes del cambio
            foreach ($numbers as $number) {
                $this->logStatusChange($number, $number->status, 'disponible', 'bulk_release', [
                    'bulk_data' => [
                        'total_numbers' => count($numberIds),
                        'bulk_action_date' => now()->toISOString()
                    ]
                ]);
            }

            // Liberar números (marcar como disponibles y desasignar participantes)
            $updatedCount = Number::whereIn('id', $numberIds)
                ->whereIn('status', ['reservado', 'pagado'])
                ->update([
                    'status' => 'disponible',
                    'participant_id' => null
                ]);

            $message = $updatedCount === 1
                ? '1 número liberado correctamente'
                : "{$updatedCount} números liberados correctamente";

            return back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Error en bulkRelease: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
        }
    }
}
