<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Raffle;
use App\Models\Participant;
use App\Models\Number;
use App\Models\Prize;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalRaffles = Raffle::count();
        $totalParticipants = Participant::count();
        $totalNumbers = Number::count();
        $totalPrizes = Prize::count();

        // Rifa más reciente
        $latestRaffle = Raffle::with('numbers')->latest()->first();

        // Estadísticas de la rifa más reciente
        $raffleStats = null;
        if ($latestRaffle) {
            $totalNumbersInRaffle = $latestRaffle->numbers->count();
            $soldNumbers = $latestRaffle->numbers->where('status', 'pagado')->count();
            $availableNumbers = $latestRaffle->numbers->where('status', 'disponible')->count();
            $reservedNumbers = $latestRaffle->numbers->where('status', 'reservado')->count();

            $raffleStats = [
                'total' => $totalNumbersInRaffle,
                'sold' => $soldNumbers,
                'available' => $availableNumbers,
                'reserved' => $reservedNumbers,
                'percentage_sold' => $totalNumbersInRaffle > 0 ? round(($soldNumbers / $totalNumbersInRaffle) * 100, 1) : 0,
                'percentage_available' => $totalNumbersInRaffle > 0 ? round(($availableNumbers / $totalNumbersInRaffle) * 100, 1) : 0
            ];
        }

        // Top participantes (con más números)
        $topParticipants = Participant::withCount('numbers')
            ->orderBy('numbers_count', 'desc')
            ->limit(5)
            ->get();

        // Rifas recientes
        $recentRaffles = Raffle::with('numbers')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($raffle) {
                $totalNumbers = $raffle->numbers->count();
                $soldNumbers = $raffle->numbers->where('status', 'pagado')->count();
                $percentage = $totalNumbers > 0 ? round(($soldNumbers / $totalNumbers) * 100, 1) : 0;

                return [
                    'id' => $raffle->id,
                    'name' => $raffle->name,
                    'draw_date' => $raffle->draw_date,
                    'total_numbers' => $totalNumbers,
                    'sold_numbers' => $soldNumbers,
                    'percentage' => $percentage
                ];
            });

        // Gráfico de ventas por día (últimos 30 días)
        $salesByDay = Number::where('status', 'pagado')
            ->where('updated_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Estadísticas de premios
        $prizesStats = Prize::with('raffle')
            ->get()
            ->groupBy('raffle_id')
            ->map(function($prizes, $raffleId) {
                $raffle = Raffle::find($raffleId);
                return [
                    'raffle_name' => $raffle ? $raffle->name : 'Rifa no encontrada',
                    'prizes_count' => $prizes->count(),
                    'prizes' => $prizes->pluck('name')->toArray()
                ];
            });

        return view('admin.dashboard', compact(
            'totalRaffles',
            'totalParticipants',
            'totalNumbers',
            'totalPrizes',
            'latestRaffle',
            'raffleStats',
            'topParticipants',
            'recentRaffles',
            'salesByDay',
            'prizesStats'
        ));
    }

    public function statistics()
    {
        // Estadísticas detalladas para AJAX
        $raffles = Raffle::with('numbers')->get();

        $statistics = $raffles->map(function($raffle) {
            $totalNumbers = $raffle->numbers->count();
            $soldNumbers = $raffle->numbers->where('status', 'pagado')->count();
            $availableNumbers = $raffle->numbers->where('status', 'disponible')->count();
            $reservedNumbers = $raffle->numbers->where('status', 'reservado')->count();

            return [
                'id' => $raffle->id,
                'name' => $raffle->name,
                'total_numbers' => $totalNumbers,
                'sold_numbers' => $soldNumbers,
                'available_numbers' => $availableNumbers,
                'reserved_numbers' => $reservedNumbers,
                'percentage_sold' => $totalNumbers > 0 ? round(($soldNumbers / $totalNumbers) * 100, 1) : 0,
                'percentage_available' => $totalNumbers > 0 ? round(($availableNumbers / $totalNumbers) * 100, 1) : 0
            ];
        });

        return response()->json($statistics);
    }
}
