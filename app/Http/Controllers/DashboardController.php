<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Raffle;
use App\Models\Participant;
use App\Models\Number;
use App\Models\Prize;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user && $user->is_admin) {
            // Panel Admin (reutiliza cálculos del panel admin actual)
            $totalRaffles = Raffle::count();
            $totalParticipants = Participant::count();
            $totalNumbers = Number::count();
            $totalPrizes = Prize::count();

            $latestRaffle = Raffle::with('numbers')->latest()->first();

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
                    'percentage_available' => $totalNumbersInRaffle > 0 ? round(($availableNumbers / $totalNumbersInRaffle) * 100, 1) : 0,
                ];
            }

            $topParticipants = Participant::withCount('numbers')
                ->orderBy('numbers_count', 'desc')
                ->limit(5)
                ->get();

            $recentRaffles = Raffle::with('numbers')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($raffle) {
                    $totalNumbers = $raffle->numbers->count();
                    $soldNumbers = $raffle->numbers->where('status', 'pagado')->count();
                    $percentage = $totalNumbers > 0 ? round(($soldNumbers / $totalNumbers) * 100, 1) : 0;
                    return [
                        'id' => $raffle->id,
                        'name' => $raffle->name,
                        'draw_date' => $raffle->draw_date,
                        'total_numbers' => $totalNumbers,
                        'sold_numbers' => $soldNumbers,
                        'percentage' => $percentage,
                    ];
                });

            $salesByDay = Number::where('status', 'pagado')
                ->where('updated_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(updated_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return view('dashboard', compact(
                'totalRaffles',
                'totalParticipants',
                'totalNumbers',
                'totalPrizes',
                'latestRaffle',
                'raffleStats',
                'topParticipants',
                'recentRaffles',
                'salesByDay'
            ));
        }

        // Panel usuario (no admin)
        $myNumbers = collect();
        if ($user) {
            $myNumbers = Number::with('raffle')
                ->whereHas('participant', function ($q) use ($user) {
                    $q->where('email', $user->email)->orWhere('phone', $user->phone);
                })
                ->orderByDesc('updated_at')
                ->limit(20)
                ->get();
        }

        $upcomingRaffles = Raffle::whereDate('draw_date', '>=', now()->toDateString())
            ->orderBy('draw_date')
            ->limit(6)
            ->get();

        return view('dashboard', compact('myNumbers', 'upcomingRaffles'));
    }
}
