<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Number;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $participants = Participant::with(['numbers.raffle'])->paginate(15);
        return view('admin.participants.index', compact('participants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.participants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(Participant::getValidationRules());

        $participant = Participant::create($request->except('photo'));

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $filename = 'participant_' . time() . '_' . Str::slug($participant->name) . '.' . $extension;
            $path = $file->storeAs('participants', $filename, 'public');
            $participant->photo = $path;
            $participant->save();
        }

        return redirect()->route('admin.participants.index')->with('success', 'Participante creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $participant = Participant::with(['numbers.raffle'])->findOrFail($id);
        return view('admin.participants.show', compact('participant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $participant = Participant::findOrFail($id);
        return view('admin.participants.edit', compact('participant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $participant = Participant::findOrFail($id);

        $request->validate(Participant::getValidationRules($id));

        $participant->update($request->except('photo'));

        if ($request->hasFile('photo')) {
            // Eliminar foto anterior si existe
            if ($participant->photo) {
                Storage::disk('public')->delete($participant->photo);
            }

            $file = $request->file('photo');
            $extension = $file->getClientOriginalExtension();
            $filename = 'participant_' . time() . '_' . Str::slug($participant->name) . '.' . $extension;
            $path = $file->storeAs('participants', $filename, 'public');
            $participant->photo = $path;
            $participant->save();
        }

        return redirect()->route('admin.participants.index')->with('success', 'Participante actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $participant = Participant::findOrFail($id);

        // Eliminar foto si existe
        if ($participant->photo) {
            Storage::disk('public')->delete($participant->photo);
        }

        $participant->delete();

        return redirect()->route('admin.participants.index')->with('success', 'Participante eliminado correctamente');
    }

    /**
     * Release a number from a participant
     */
    public function releaseNumber(Request $request, string $participantId)
    {
        $request->validate([
            'number_id' => 'required|exists:numbers,id'
        ]);

        $participant = Participant::findOrFail($participantId);
        $number = Number::findOrFail($request->number_id);

        // Verificar que el número pertenece al participante
        if ($number->participant_id != $participant->id) {
            return response()->json(['error' => 'El número no pertenece a este participante'], 400);
        }

        // Liberar el número
        $number->participant_id = null;
        $number->status = 'disponible';
        $number->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => 'Número liberado correctamente',
                'number' => $number->number,
                'raffle' => $number->raffle->name
            ]);
        }

        return redirect()->back()->with('success', 'Número liberado correctamente');
    }
}
