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
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $participantsQuery = Participant::with(['numbers.raffle'])->orderByDesc('created_at');

        if ($q !== '') {
            $participantsQuery->where(function($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                  ->orWhere('email', 'like', "%$q%")
                  ->orWhere('phone', 'like', "%$q%")
                  ->orWhereHas('numbers', function($n) use ($q) {
                      $n->where('number', 'like', "%$q%")
                        ->orWhere('status', 'like', "%$q%")
                        ->orWhereHas('raffle', function($r) use ($q) {
                            $r->where('name', 'like', "%$q%");
                        });
                  });
            });
        }

        $participants = $participantsQuery->paginate(15)->appends($request->query());
        return view('admin.participants.index', compact('participants', 'q'));
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

        // Normalizar teléfono antes de crear
        if ($request->phone) {
            $request->merge(['phone' => Participant::normalizePeruPhone($request->phone)]);
        }

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
        $participant = Participant::with(['numbers.raffle'])->findOrFail($id);
        return view('admin.participants.edit', compact('participant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $participant = Participant::findOrFail($id);

        $request->validate(Participant::getValidationRules($id));

        // Normalizar teléfono antes de actualizar
        if ($request->phone) {
            $request->merge(['phone' => Participant::normalizePeruPhone($request->phone)]);
        }

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
     * Release a number assigned to a participant
     */
    public function releaseNumber(Request $request, string $id)
    {
        $request->validate([
            'number_id' => 'required|exists:numbers,id'
        ]);

        $participant = Participant::findOrFail($id);
        $number = Number::findOrFail($request->number_id);

        // Verificar que el número pertenece al participante
        if ($number->participant_id != $participant->id) {
            return response()->json(['error' => 'El número no pertenece a este participante'], 400);
        }

        // Liberar el número
        $number->participant_id = null;
        $number->status = 'disponible';
        $number->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => 'Número liberado correctamente']);
        }

        return redirect()->back()->with('success', 'Número liberado correctamente');
    }
}
