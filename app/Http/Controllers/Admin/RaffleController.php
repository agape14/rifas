<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Raffle;
use App\Models\Prize;
use App\Models\Number;
use App\Models\Participant;
use App\Models\DrawResult;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RaffleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $rafflesQuery = Raffle::with(['numbers', 'prizes'])->orderByDesc('created_at');

        if ($q !== '') {
            $rafflesQuery->where(function($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                  ->orWhere('status', 'like', "%$q%")
                  ->orWhere('draw_date', 'like', "%$q%")
                  ->orWhere('total_numbers', 'like', "%$q%");
            });
        }

        $raffles = $rafflesQuery->paginate(10)->appends($request->query());
        return view('admin.raffles.index', compact('raffles', 'q'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.raffles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
            'draw_date' => 'required|date',
            'status' => 'required|string',
            'total_numbers' => 'required|integer',
            'theme_color' => 'required|string',
            'number_price' => 'nullable|numeric|min:0',
            'terms_html' => 'nullable|string',
            'organizer_name' => 'nullable|string|max:255',
            'organizer_id' => 'nullable|string|max:255',
            'organizer_address' => 'nullable|string|max:255',
            'organizer_contact' => 'nullable|string|max:255',
            'organizer_contact_email' => 'nullable|email|max:255',
            'platform_name' => 'nullable|string|max:255',
            'broadcast_platform' => 'nullable|string|max:255',
            'privacy_url' => 'nullable|url|max:255',
            'claim_days' => 'nullable|integer|min:0',
            'jurisdiction_city' => 'nullable|string|max:255',
        ]);

        $raffle = Raffle::create($request->except('banner', 'number_price'));

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $extension = $file->getClientOriginalExtension();
            $filename = 'raffle_' . time() . '_' . Str::slug($raffle->name) . '.' . $extension;
            $path = $file->storeAs('banners', $filename, 'public');
            $raffle->banner = $path;
            $raffle->save();
        }

        $price = $request->input('number_price', 10);
        // Generar números 1..total_numbers para la rifa recién creada con precio por defecto
        $this->ensureRaffleNumbers($raffle, $price);

        return redirect()->route('admin.raffles.index')->with('success', 'Rifa creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $raffle = Raffle::findOrFail($id);
        return view('admin.raffles.show', compact('raffle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $raffle = Raffle::findOrFail($id);
        return view('admin.raffles.edit', compact('raffle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $raffle = Raffle::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
            'draw_date' => 'required|date',
            'status' => 'required|string',
            'total_numbers' => 'required|integer',
            'theme_color' => 'required|string',
            'number_price' => 'nullable|numeric|min:0',
            'terms_html' => 'nullable|string',
            'organizer_name' => 'nullable|string|max:255',
            'organizer_id' => 'nullable|string|max:255',
            'organizer_address' => 'nullable|string|max:255',
            'organizer_contact' => 'nullable|string|max:255',
            'organizer_contact_email' => 'nullable|email|max:255',
            'platform_name' => 'nullable|string|max:255',
            'broadcast_platform' => 'nullable|string|max:255',
            'privacy_url' => 'nullable|url|max:255',
            'claim_days' => 'nullable|integer|min:0',
            'jurisdiction_city' => 'nullable|string|max:255',
        ]);

        // --- Validación de cambios en total_numbers antes de actualizar el modelo ---
        $desiredTotal = (int) $request->input('total_numbers');
        $currentCount = $raffle->numbers()->count();
        $hasReservedOrPaid = $raffle->numbers()->whereIn('status', ['reservado', 'pagado'])->exists();

        $action = 'noop'; // noop | increase_only | regenerate
        if ($desiredTotal !== $currentCount) {
            if ($hasReservedOrPaid) {
                if ($desiredTotal < $currentCount) {
                    // No permitir reducir cuando hay reservados/pagados
                    return redirect()
                        ->route('admin.raffles.edit', $raffle->id)
                        ->withErrors(['total_numbers' => 'No se puede reducir la cantidad de números porque existen números reservados o pagados. Solo se permite aumentar.'])
                        ->withInput();
                }
                // Aumentar: permitido, se añadirán faltantes
                $action = 'increase_only';
            } else {
                // Todos disponibles: se puede aumentar o reducir, se regenera exacto
                $action = 'regenerate';
            }
        }

        // Actualizar datos de la rifa (incluye total_numbers permitido)
        $raffle->update($request->except('banner', 'number_price'));

        if ($request->hasFile('banner')) {
            // Eliminar imagen anterior si existe
            if ($raffle->banner) {
                Storage::disk('public')->delete($raffle->banner);
            }

            $file = $request->file('banner');
            $extension = $file->getClientOriginalExtension();
            $filename = 'raffle_' . time() . '_' . Str::slug($raffle->name) . '.' . $extension;
            $path = $file->storeAs('banners', $filename, 'public');
            $raffle->banner = $path;
            $raffle->save();
        }

        $price = $request->input('number_price');

        // Ejecutar acción de números dependiendo de la validación previa
        if ($action === 'increase_only') {
            // añade faltantes hasta total_numbers con el precio indicado (o mantener precio actual si no se envió)
            $this->ensureRaffleNumbers($raffle, $price ?? 10);
        } elseif ($action === 'regenerate') {
            // recrea exacto 1..total_numbers con el precio indicado
            $this->regenerateRaffleNumbers($raffle, $price ?? 10);
        }

        // Si se especificó un precio, actualizar todos los números existentes de la rifa con ese precio
        if ($price !== null && $price !== '') {
            $raffle->numbers()->update(['price' => $price]);
        }

        return redirect()->route('admin.raffles.index')->with('success', 'Rifa actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $raffle = Raffle::findOrFail($id);

        // Eliminar imagen si existe
        if ($raffle->banner) {
            Storage::disk('public')->delete($raffle->banner);
        }

        $raffle->delete();

        return redirect()->route('admin.raffles.index')->with('success', 'Rifa eliminada correctamente');
    }

    public function qr(Raffle $raffle)
    {
        // Generar data de QR (URL pública de la rifa)
        $url = route('public.raffle.show', $raffle->id);
        return view('admin.raffles.qr', compact('raffle', 'url'));
    }

    public function poster(Raffle $raffle)
    {
        $url = route('public.raffle.show', $raffle->id);
        return view('admin.raffles.poster', compact('raffle', 'url'));
    }

    /**
     * Asegura que existan números del 1..total_numbers para la rifa.
     * Crea únicamente los que falten. No elimina sobrantes.
     */
    private function ensureRaffleNumbers(Raffle $raffle, ?float $price = null): void
    {
        $totalNumbers = (int) $raffle->total_numbers;
        if ($totalNumbers <= 0) {
            return;
        }

        // Obtener cuáles números ya existen para esta rifa
        $existingNumbers = $raffle->numbers()->pluck('number')->all();
        $existingSet = array_flip($existingNumbers);

        $defaultPrice = $price ?? 10;
        $toInsert = [];
        for ($n = 1; $n <= $totalNumbers; $n++) {
            if (!isset($existingSet[$n])) {
                $toInsert[] = [
                    'raffle_id' => $raffle->id,
                    'number' => $n,
                    'participant_id' => null,
                    'status' => 'disponible',
                    'price' => $defaultPrice,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($toInsert)) {
            Number::insert($toInsert);
        }
    }

    /**
     * Regenera exactamente los números 1..total_numbers, eliminando los existentes.
     * Debe llamarse solo si no hay números reservados/pagados.
     */
    private function regenerateRaffleNumbers(Raffle $raffle, ?float $price = null): void
    {
        $totalNumbers = (int) $raffle->total_numbers;
        if ($totalNumbers <= 0) {
            // Si se define 0, simplemente elimina los existentes
            $raffle->numbers()->delete();
            return;
        }

        // Eliminar todos los números actuales
        $raffle->numbers()->delete();

        $defaultPrice = $price ?? 10;
        // Insertar del 1 al total
        $toInsert = [];
        for ($n = 1; $n <= $totalNumbers; $n++) {
            $toInsert[] = [
                'raffle_id' => $raffle->id,
                'number' => $n,
                'participant_id' => null,
                'status' => 'disponible',
                'price' => $defaultPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Number::insert($toInsert);
    }
}
