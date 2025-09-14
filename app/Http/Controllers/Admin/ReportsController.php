<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Raffle;
use App\Models\Number;
use App\Models\DrawResult;
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

    public function winners(Request $request)
    {
        // Obtener todas las rifas con ganadores
        $raffles = Raffle::withCount(['drawResults as winners_count' => function($q) {
            $q->where('status', 'winner');
        }])
        ->having('winners_count', '>', 0)
        ->orderBy('created_at', 'desc')
        ->get();

        // Si se especifica una rifa específica, mostrar solo sus ganadores
        $raffleId = $request->query('raffle_id');
        $dateRange = $request->query('date_range');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $selectedRaffle = null;
        $winners = collect();

        // Aplicar filtros de rango de fecha automáticamente
        if ($dateRange && $dateRange !== 'custom') {
            $today = now();
            switch ($dateRange) {
                case 'today':
                    $dateFrom = $today->format('Y-m-d');
                    $dateTo = $today->format('Y-m-d');
                    break;
                case '7days':
                    $dateFrom = $today->subDays(7)->format('Y-m-d');
                    $dateTo = $today->format('Y-m-d');
                    break;
                case '15days':
                    $dateFrom = $today->subDays(15)->format('Y-m-d');
                    $dateTo = $today->format('Y-m-d');
                    break;
                case '30days':
                    $dateFrom = $today->subDays(30)->format('Y-m-d');
                    $dateTo = $today->format('Y-m-d');
                    break;
                case 'quarter':
                    $quarterStart = $today->copy()->startOfQuarter();
                    $dateFrom = $quarterStart->format('Y-m-d');
                    $dateTo = $today->format('Y-m-d');
                    break;
                case 'semester':
                    $semesterStart = $today->month < 7 ? $today->copy()->startOfYear() : $today->copy()->month(7)->startOfMonth();
                    $dateFrom = $semesterStart->format('Y-m-d');
                    $dateTo = $today->format('Y-m-d');
                    break;
                case 'year':
                    $dateFrom = $today->copy()->startOfYear()->format('Y-m-d');
                    $dateTo = $today->format('Y-m-d');
                    break;
            }
        }

        if ($raffleId) {
            $selectedRaffle = Raffle::findOrFail($raffleId);

            $winnersQuery = DrawResult::with(['participant', 'prize'])
                ->where('raffle_id', $raffleId)
                ->where('status', 'winner');

            // Aplicar filtros de fecha si se proporcionan
            if ($dateFrom) {
                $winnersQuery->whereDate('drawn_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $winnersQuery->whereDate('drawn_at', '<=', $dateTo);
            }

            $winners = $winnersQuery->orderBy('drawn_at')->get();
        }

        return view('admin.reports.winners', compact('raffles', 'selectedRaffle', 'winners', 'dateFrom', 'dateTo'));
    }

    public function exportWinnersCsv(Request $request): StreamedResponse
    {
        try {
            $raffleId = $request->query('raffle_id');
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');

            $raffle = Raffle::findOrFail($raffleId);

            // Generar nombre de archivo limpio
            $raffleName = preg_replace('/[^A-Za-z0-9\s]/', '', $raffle->name);
            $raffleName = preg_replace('/\s+/', '_', trim($raffleName));
            $dateRange = '';

            if ($dateFrom && $dateTo) {
                if ($dateFrom === $dateTo) {
                    $dateRange = '_' . $dateFrom;
                } else {
                    $dateRange = '_' . $dateFrom . '_a_' . $dateTo;
                }
            } elseif ($dateFrom) {
                $dateRange = '_desde_' . $dateFrom;
            } elseif ($dateTo) {
                $dateRange = '_hasta_' . $dateTo;
            }

            $fileName = 'ganadores_' . $raffleName . $dateRange . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
            ];

            $callback = function() use ($raffle, $dateFrom, $dateTo) {
                $handle = fopen('php://output', 'w');

                // Agregar BOM para UTF-8
                fwrite($handle, "\xEF\xBB\xBF");

                // Encabezados
                fputcsv($handle, ['Rifa', $raffle->name]);
                fputcsv($handle, ['Fecha del sorteo', $raffle->draw_date]);

                // Información de filtros
                if ($dateFrom || $dateTo) {
                    fputcsv($handle, ['Período filtrado', ($dateFrom ?: 'Inicio') . ' - ' . ($dateTo ?: 'Fin')]);
                }

                fputcsv($handle, []); // Línea vacía
                fputcsv($handle, ['Premio', 'Número Ganador', 'Ganador', 'Teléfono', 'Email', 'Fecha de Sorteo']);

                // Construir query con filtros
                $winnersQuery = DrawResult::with(['participant', 'prize'])
                    ->where('raffle_id', $raffle->id)
                    ->where('status', 'winner');

                // Aplicar filtros de fecha si se proporcionan
                if ($dateFrom) {
                    $winnersQuery->whereDate('drawn_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $winnersQuery->whereDate('drawn_at', '<=', $dateTo);
                }

                $winners = $winnersQuery->orderBy('drawn_at')->get();

                foreach ($winners as $winner) {
                    fputcsv($handle, [
                        $winner->prize->name ?? 'Sin premio',
                        $winner->number,
                        $winner->participant->name ?? 'Sin nombre',
                        $winner->participant->phone ?? '',
                        $winner->participant->email ?? '',
                        $winner->drawn_at->format('d/m/Y H:i:s')
                    ]);
                }

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Error al generar CSV de ganadores: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al generar el CSV: ' . $e->getMessage()]);
        }
    }

    public function exportWinnersPdf(Request $request)
    {
        try {
            $raffleId = $request->query('raffle_id');
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');

            $raffle = Raffle::findOrFail($raffleId);

            $winnersQuery = DrawResult::with(['participant', 'prize'])
                ->where('raffle_id', $raffleId)
                ->where('status', 'winner');

            // Aplicar filtros de fecha si se proporcionan
            if ($dateFrom) {
                $winnersQuery->whereDate('drawn_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $winnersQuery->whereDate('drawn_at', '<=', $dateTo);
            }

            $winners = $winnersQuery->orderBy('drawn_at')->get();

            // Generar nombre de archivo limpio
            $raffleName = preg_replace('/[^A-Za-z0-9\s]/', '', $raffle->name);
            $raffleName = preg_replace('/\s+/', '_', trim($raffleName));
            $dateRange = '';

            if ($dateFrom && $dateTo) {
                if ($dateFrom === $dateTo) {
                    $dateRange = '_' . $dateFrom;
                } else {
                    $dateRange = '_' . $dateFrom . '_a_' . $dateTo;
                }
            } elseif ($dateFrom) {
                $dateRange = '_desde_' . $dateFrom;
            } elseif ($dateTo) {
                $dateRange = '_hasta_' . $dateTo;
            }

            $filename = 'ganadores_' . $raffleName . $dateRange . '.pdf';

            // Configurar PDF con opciones más simples
            $pdf = \PDF::loadView('admin.reports.winners-pdf', compact('raffle', 'winners', 'dateFrom', 'dateTo'))
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'isPhpEnabled' => false,
                    'isJavascriptEnabled' => false,
                ]);

            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Error al generar PDF de ganadores: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al generar el PDF: ' . $e->getMessage()]);
        }
    }

    // Método de prueba para verificar DomPDF
    public function testPdf()
    {
        try {
            $html = '<html><body><h1>Prueba de PDF</h1><p>Este es un PDF de prueba generado correctamente.</p></body></html>';

            $pdf = \PDF::loadHTML($html)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'isPhpEnabled' => false,
                    'isJavascriptEnabled' => false,
                ]);

            return $pdf->download('test_pdf.pdf');

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Método alternativo para exportar PDF con vista simple
    public function exportWinnersPdfSimple(Request $request)
    {
        try {
            $raffleId = $request->query('raffle_id');
            $dateFrom = $request->query('date_from');
            $dateTo = $request->query('date_to');

            $raffle = Raffle::findOrFail($raffleId);

            $winnersQuery = DrawResult::with(['participant', 'prize'])
                ->where('raffle_id', $raffleId)
                ->where('status', 'winner');

            // Aplicar filtros de fecha si se proporcionan
            if ($dateFrom) {
                $winnersQuery->whereDate('drawn_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $winnersQuery->whereDate('drawn_at', '<=', $dateTo);
            }

            $winners = $winnersQuery->orderBy('drawn_at')->get();

            // Generar nombre de archivo limpio
            $raffleName = preg_replace('/[^A-Za-z0-9\s]/', '', $raffle->name);
            $raffleName = preg_replace('/\s+/', '_', trim($raffleName));
            $dateRange = '';

            if ($dateFrom && $dateTo) {
                if ($dateFrom === $dateTo) {
                    $dateRange = '_' . $dateFrom;
                } else {
                    $dateRange = '_' . $dateFrom . '_a_' . $dateTo;
                }
            } elseif ($dateFrom) {
                $dateRange = '_desde_' . $dateFrom;
            } elseif ($dateTo) {
                $dateRange = '_hasta_' . $dateTo;
            }

            $filename = 'ganadores_' . $raffleName . $dateRange . '.pdf';

            // Crear HTML simple para el PDF
            $html = '<html><head><meta charset="UTF-8"><title>Reporte de Ganadores</title></head><body>';
            $html .= '<h1>Reporte de Ganadores</h1>';
            $html .= '<h2>' . htmlspecialchars($raffle->name) . '</h2>';

            if ($dateFrom || $dateTo) {
                $html .= '<p><strong>Período:</strong> ' . ($dateFrom ?: 'Inicio') . ' - ' . ($dateTo ?: 'Fin') . '</p>';
            }

            $html .= '<p><strong>Total de ganadores:</strong> ' . $winners->count() . '</p>';

            if ($winners->count() > 0) {
                $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse:collapse;">';
                $html .= '<tr style="background-color:#f0f0f0;">';
                $html .= '<th>#</th><th>Número</th><th>Ganador</th><th>Teléfono</th><th>Email</th><th>Premio</th><th>Fecha</th>';
                $html .= '</tr>';

                foreach ($winners as $index => $winner) {
                    $html .= '<tr>';
                    $html .= '<td>' . ($index + 1) . '</td>';
                    $html .= '<td>' . htmlspecialchars($winner->number) . '</td>';
                    $html .= '<td>' . htmlspecialchars($winner->participant->name ?? 'Sin nombre') . '</td>';
                    $html .= '<td>' . htmlspecialchars($winner->participant->phone ?? '') . '</td>';
                    $html .= '<td>' . htmlspecialchars($winner->participant->email ?? '') . '</td>';
                    $html .= '<td>' . htmlspecialchars($winner->prize->name ?? 'Sin premio') . '</td>';
                    $html .= '<td>' . $winner->drawn_at->format('d/m/Y H:i') . '</td>';
                    $html .= '</tr>';
                }

                $html .= '</table>';
            } else {
                $html .= '<p>No se encontraron ganadores en el período seleccionado.</p>';
            }

            $html .= '<p style="margin-top:30px; font-size:10px; color:#666;">Generado el: ' . now()->format('d/m/Y H:i:s') . '</p>';
            $html .= '</body></html>';

            $pdf = \PDF::loadHTML($html)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'isPhpEnabled' => false,
                    'isJavascriptEnabled' => false,
                ]);

            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Error al generar PDF simple de ganadores: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al generar el PDF: ' . $e->getMessage()]);
        }
    }
}
