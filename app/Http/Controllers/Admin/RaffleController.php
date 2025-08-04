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
    public function index()
    {
        $raffles = Raffle::with(['numbers', 'prizes'])->paginate(10);
        return view('admin.raffles.index', compact('raffles'));
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
        ]);

        $raffle = Raffle::create($request->except('banner'));

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $extension = $file->getClientOriginalExtension();
            $filename = 'raffle_' . time() . '_' . Str::slug($raffle->name) . '.' . $extension;
            $path = $file->storeAs('banners', $filename, 'public');
            $raffle->banner = $path;
            $raffle->save();
        }

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
        ]);

        $raffle->update($request->except('banner'));

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
}
