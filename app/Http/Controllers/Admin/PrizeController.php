<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prize;
use App\Models\Raffle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prizes = Prize::with('raffle')->paginate(10);
        return view('admin.prizes.index', compact('prizes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $raffles = Raffle::all();
        return view('admin.prizes.create', compact('raffles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'raffle_id' => 'required|exists:raffles,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $prize = Prize::create($request->except('image'));

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = 'prize_' . time() . '_' . Str::slug($prize->name) . '.' . $extension;
            $path = $file->storeAs('prizes', $filename, 'public');
            $prize->image = $path;
            $prize->save();
        }

        return redirect()->route('admin.prizes.index')->with('success', 'Premio creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $prize = Prize::with('raffle')->findOrFail($id);
        return view('admin.prizes.show', compact('prize'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $prize = Prize::findOrFail($id);
        $raffles = Raffle::all();
        return view('admin.prizes.edit', compact('prize', 'raffles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $prize = Prize::findOrFail($id);

        $request->validate([
            'raffle_id' => 'required|exists:raffles,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $prize->update($request->except('image'));

        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($prize->image) {
                Storage::disk('public')->delete($prize->image);
            }

            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = 'prize_' . time() . '_' . Str::slug($prize->name) . '.' . $extension;
            $path = $file->storeAs('prizes', $filename, 'public');
            $prize->image = $path;
            $prize->save();
        }

        return redirect()->route('admin.prizes.index')->with('success', 'Premio actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $prize = Prize::findOrFail($id);

        // Eliminar imagen si existe
        if ($prize->image) {
            Storage::disk('public')->delete($prize->image);
        }

        $prize->delete();

        return redirect()->route('admin.prizes.index')->with('success', 'Premio eliminado correctamente');
    }
}
