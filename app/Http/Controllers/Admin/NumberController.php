<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Number;
use App\Models\Raffle;

class NumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $raffleId = $request->query('raffle_id');

        $query = Number::with(['raffle', 'participant']);
        $currentRaffle = null;

        if ($raffleId) {
            $query->where('raffle_id', $raffleId);
            $currentRaffle = Raffle::find($raffleId);
        }

        $numbers = $query->paginate(20);

        return view('admin.numbers.index', compact('numbers', 'currentRaffle'));
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
        $number = Number::findOrFail($id);
        if ($number->status !== 'reservado') {
            return back()->withErrors(['status' => 'Solo se puede marcar como pagado un número reservado']);
        }
        $number->status = 'pagado';
        $number->save();
        return back()->with('success', 'Número marcado como pagado');
    }

    public function release(string $id)
    {
        $number = Number::findOrFail($id);
        $number->participant_id = null;
        $number->status = 'disponible';
        $number->save();
        return back()->with('success', 'Número liberado correctamente');
    }
}
