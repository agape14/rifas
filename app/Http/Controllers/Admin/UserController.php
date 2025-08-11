<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Raffle;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $raffleId = $request->query('raffle_id');

        $query = User::with('managedRaffle')->orderBy('name');
        if ($q) {
            $query->where(function($qq) use ($q) {
                $qq->where('name', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%");
            });
        }
        if ($raffleId) {
            $query->where('managed_raffle_id', $raffleId);
        }

        $users = $query->paginate(20)->appends($request->query());
        $raffles = Raffle::orderBy('name')->get();
        return view('admin.users.index', compact('users', 'raffles', 'q', 'raffleId'));
    }

    public function edit(User $user)
    {
        $raffles = Raffle::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'raffles'));
    }

    public function update(Request $request, User $user)
    {
        // Limpieza rápida de rifa asignada
        if ($request->has('clear_assignment')) {
            $user->managed_raffle_id = null;
            $user->save();
            return redirect()->route('admin.users.index')->with('success', 'Rifa asignada removida del usuario');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'managed_raffle_id' => 'nullable|exists:raffles,id',
            'is_admin' => 'nullable|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->managed_raffle_id = $validated['managed_raffle_id'] ?? null;
        if ($request->has('is_admin')) {
            $user->is_admin = (bool)$validated['is_admin'];
        }
        if (!empty($validated['password'])) {
            // Se auto-hasheará por el cast del modelo
            $user->password = $validated['password'];
        }
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado');
    }
}
