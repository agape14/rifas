<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Raffle;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $request->expectsJson()
                ? response()->json(['error' => 'No autenticado'], 401)
                : redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->is_admin) {
            return $next($request);
        }

        // Manager de rifa: permitir si la ruta apunta a su rifa
        $raffleId = $request->route('raffle') instanceof Raffle
            ? $request->route('raffle')->id
            : ($request->route('id') ?? $request->query('raffle_id'));

        if ($raffleId && (int)$user->managed_raffle_id === (int)$raffleId) {
            return $next($request);
        }

        return $request->expectsJson()
            ? response()->json(['error' => 'No tienes permisos'], 403)
            : abort(403, 'No tienes permisos');
    }
}
