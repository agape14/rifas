<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Raffle;
use App\Models\Number;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function index()
    {
        $raffles = Raffle::withCount(['numbers as paid_numbers_count' => function($q){
            $q->where('status', 'pagado');
        }])->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.reports.index', compact('raffles'));
    }

    public function raffle(Raffle $raffle)
    {
        $raffle->load(['numbers.participant', 'prizes']);
        $numbers = $raffle->numbers()->with('participant')->orderBy('number')->get();

        $summary = [
            'total' => $numbers->count(),
            'paid' => $numbers->where('status', 'pagado')->count(),
            'reserved' => $numbers->where('status', 'reservado')->count(),
            'available' => $numbers->where('status', 'disponible')->count(),
        ];

        return view('admin.reports.raffle', compact('raffle', 'numbers', 'summary'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $raffleId = $request->query('raffle_id');
        $raffle = Raffle::findOrFail($raffleId);
        $fileName = 'reporte_rifa_'.$raffle->id.'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($raffle) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Raffle', $raffle->name]);
            fputcsv($handle, ['Numero', 'Estado', 'Participante', 'Telefono', 'Email']);

            $raffle->numbers()->with('participant')->orderBy('number')->chunk(500, function($chunk) use ($handle) {
                foreach ($chunk as $num) {
                    fputcsv($handle, [
                        $num->number,
                        $num->status,
                        optional($num->participant)->name,
                        optional($num->participant)->phone,
                        optional($num->participant)->email,
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
