<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Raffle;
use App\Models\Number;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Models\ProposalSetting;

class ReportsController extends Controller
{
    public function index()
    {
        $raffles = Raffle::withCount(['numbers as paid_numbers_count' => function($q){
            $q->where('status', 'pagado');
        }])->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.reports.index', compact('raffles'));
    }

    public function raffle(Request $request, Raffle $raffle)
    {
        // Resumen general (no filtrado)
        $raffle->load(['numbers.participant', 'prizes']);
        $allNumbers = $raffle->numbers;
        $summary = [
            'total' => $allNumbers->count(),
            'paid' => $allNumbers->where('status', 'pagado')->count(),
            'reserved' => $allNumbers->where('status', 'reservado')->count(),
            'available' => $allNumbers->where('status', 'disponible')->count(),
        ];

        // Lista filtrada/paginada
        $q = trim((string) $request->query('q', ''));
        $numbersQuery = Number::with('participant')
            ->where('raffle_id', $raffle->id)
            ->orderBy('number');

        if ($q !== '') {
            $numbersQuery->where(function($qq) use ($q) {
                $qq->where('number', 'like', "%$q%")
                   ->orWhere('status', 'like', "%$q%")
                   ->orWhereHas('participant', function($p) use ($q) {
                       $p->where('name', 'like', "%$q%")
                         ->orWhere('phone', 'like', "%$q%")
                         ->orWhere('email', 'like', "%$q%");
                   });
            });
        }

        $numbers = $numbersQuery->paginate(50)->appends($request->query());

        return view('admin.reports.raffle', compact('raffle', 'numbers', 'summary', 'q'));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $raffleId = $request->query('raffle_id');
        $raffle = Raffle::findOrFail($raffleId);
        $q = trim((string) $request->query('q', ''));
        $isExcel = $request->boolean('excel');

        $ext = $isExcel ? 'xls' : 'csv';
        $fileName = 'reporte_rifa_'.$raffle->id.'.'.$ext;

        $headers = [
            'Content-Type' => $isExcel ? 'application/vnd.ms-excel' : 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($raffle, $q) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Raffle', $raffle->name]);
            fputcsv($handle, ['Numero', 'Estado', 'Participante', 'Telefono', 'Email']);

            $numbersQuery = $raffle->numbers()->with('participant')->orderBy('number');
            if ($q !== '') {
                $numbersQuery->where(function($qq) use ($q) {
                    $qq->where('number', 'like', "%$q%")
                       ->orWhere('status', 'like', "%$q%")
                       ->orWhereHas('participant', function($p) use ($q) {
                           $p->where('name', 'like', "%$q%")
                             ->orWhere('phone', 'like', "%$q%")
                             ->orWhere('email', 'like', "%$q%");
                       });
                });
            }

            $numbersQuery->chunk(500, function($chunk) use ($handle) {
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

    public function proposal(Request $request)
    {
        $setting = ProposalSetting::first();
        $defaults = [
            'title' => 'Propuesta Comercial - Plataforma de Rifas y Sorteos',
            'date' => now()->format('d/m/Y H:i'),
            'features' => [
                'Publicación de rifas con banner, descripción en HTML, premios y fecha de sorteo',
                'Cuadrícula interactiva de números (disponible, reservado, pagado)',
                'Reserva pública y asignación/venta por administrador',
                'Liberación de números por administrador',
                'Sorteo en vivo tipo ruleta con historial y modal de ganadores',
                'Reportes y exportaciones (CSV) por rifa',
                'Generación de QR y poster publicitario para difusión',
                'Soporte para múltiples rifas concurrentes',
                'Modo oscuro/claro y navegación adaptable',
                'Autenticación y roles (admin/usuario)',
            ],
            'pricing' => [
                ['segment' => 'Emprendedor', 'size' => 'Pequeño (≤ S/. 1,000)', 'estimate' => 1000, 'fixed' => 50, 'fee_pct' => 5, 'fee' => 50, 'optional' => 'Difusión redes + S/. 30'],
                ['segment' => 'Emprendedor', 'size' => 'Mediano (S/. 1,001 - 3,000)', 'estimate' => 2000, 'fixed' => 50, 'fee_pct' => 7, 'fee' => 140, 'optional' => 'Difusión redes + S/. 40'],
                ['segment' => 'Negocio Pequeño', 'size' => 'Mediano (S/. 1,001 - 3,000)', 'estimate' => 2500, 'fixed' => 80, 'fee_pct' => 8, 'fee' => 200, 'optional' => 'Difusión redes + S/. 40'],
                ['segment' => 'Negocio Pequeño', 'size' => 'Grande (S/. 3,001 - 6,000)', 'estimate' => 4000, 'fixed' => 100, 'fee_pct' => 8, 'fee' => 320, 'optional' => 'Difusión redes + S/. 50'],
                ['segment' => 'Empresa / Marca', 'size' => 'Grande (S/. 3,001 - 6,000)', 'estimate' => 5000, 'fixed' => 150, 'fee_pct' => 10, 'fee' => 500, 'optional' => 'Difusión redes + S/. 60'],
                ['segment' => 'Empresa / Marca', 'size' => 'Premium (> S/. 6,000)', 'estimate' => 10000, 'fixed' => 200, 'fee_pct' => 10, 'fee' => 1000, 'optional' => 'Difusión redes + S/. 80'],
            ],
        ];

        $data = $defaults;
        if ($setting) {
            if (is_array($setting->features) && count($setting->features)) {
                $data['features'] = $setting->features;
            }
            if (is_array($setting->pricing) && count($setting->pricing)) {
                $data['pricing'] = $setting->pricing;
            }
        }

        if ($request->boolean('download')) {
            $pdf = PDF::loadView('admin.reports.proposal_pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'DejaVu Sans'
                ]);
            return $pdf->download('propuesta_comercial_rifas.pdf');
        }

        return view('admin.reports.proposal', $data);
    }

    public function proposalEdit()
    {
        $setting = ProposalSetting::first();
        return view('admin.reports.proposal_edit', [
            'features' => $setting->features ?? [],
            'pricing' => $setting->pricing ?? [],
        ]);
    }

    public function proposalUpdate(Request $request)
    {
        $validated = $request->validate([
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'pricing' => 'nullable|array',
            'pricing.*.segment' => 'required|string',
            'pricing.*.size' => 'required|string',
            'pricing.*.estimate' => 'required|numeric',
            'pricing.*.fixed' => 'required|numeric',
            'pricing.*.fee_pct' => 'required|numeric',
            'pricing.*.fee' => 'nullable|numeric',
            'pricing.*.optional' => 'nullable|string',
        ]);

        $features = $validated['features'] ?? [];
        $pricing = $validated['pricing'] ?? [];

        // Calcular fee si no llega o está vacío
        foreach ($pricing as $idx => $row) {
            $estimate = (float)($row['estimate'] ?? 0);
            $feePct = (float)($row['fee_pct'] ?? 0);
            if (!isset($row['fee']) || $row['fee'] === '' || $row['fee'] === null) {
                $pricing[$idx]['fee'] = round($estimate * $feePct / 100);
            }
        }

        $setting = ProposalSetting::first() ?: new ProposalSetting();
        $setting->features = array_values($features);
        $setting->pricing = array_values($pricing);
        $setting->save();

        return redirect()->route('admin.reports.proposal')->with('success', 'Propuesta actualizada');
    }
}
