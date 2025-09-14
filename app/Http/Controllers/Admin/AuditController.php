<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NumberStatusAudit;
use App\Models\Raffle;
use Illuminate\Http\Request;
use Illuminate\Http\StreamedResponse;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $raffleId = $request->query('raffle_id');
        $actionType = $request->query('action_type');
        $status = $request->query('status');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // Obtener todas las rifas para el selector
        $raffles = Raffle::orderBy('name')->get();

        // Construir query base
        $query = NumberStatusAudit::with(['number', 'raffle', 'participant', 'changedBy']);

        // Aplicar filtros
        if ($raffleId) {
            $query->where('raffle_id', $raffleId);
        }

        if ($actionType) {
            $query->where('action_type', $actionType);
        }

        if ($status) {
            $query->where('new_status', $status);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $audits = $query->orderBy('created_at', 'desc')->paginate(50);

        // Estadísticas
        $stats = [
            'total_changes' => NumberStatusAudit::count(),
            'paid_changes' => NumberStatusAudit::paidStatus()->count(),
            'bulk_actions' => NumberStatusAudit::bulkActions()->count(),
            'individual_actions' => NumberStatusAudit::individualActions()->count(),
            'total_amount' => NumberStatusAudit::withAmount()->sum('amount')
        ];

        return view('admin.audit.index', compact('audits', 'raffles', 'stats', 'raffleId', 'actionType', 'status', 'dateFrom', 'dateTo'));
    }

    public function payments(Request $request)
    {
        $raffleId = $request->query('raffle_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // Obtener todas las rifas para el selector
        $raffles = Raffle::orderBy('name')->get();

        // Construir query para cambios a "pagado"
        $query = NumberStatusAudit::with(['number', 'raffle', 'participant', 'changedBy', 'paymentConfirmedBy'])
            ->paidStatus();

        // Aplicar filtros
        if ($raffleId) {
            $query->where('raffle_id', $raffleId);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(50);

        // Estadísticas de pagos
        $stats = [
            'total_payments' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'average_amount' => $query->avg('amount'),
            'bulk_payments' => $query->where('action_type', 'bulk_mark_paid')->count(),
            'individual_payments' => $query->where('action_type', 'individual')->count()
        ];

        return view('admin.audit.payments', compact('payments', 'raffles', 'stats', 'raffleId', 'dateFrom', 'dateTo'));
    }

    public function exportPaymentsCsv(Request $request): StreamedResponse
    {
        $raffleId = $request->query('raffle_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // Construir query para cambios a "pagado"
        $query = NumberStatusAudit::with(['number', 'raffle', 'participant', 'changedBy'])
            ->paidStatus();

        // Aplicar filtros
        if ($raffleId) {
            $query->where('raffle_id', $raffleId);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'reporte_cobros_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $handle = fopen('php://output', 'w');

            // BOM para UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            // Encabezados
            fputcsv($handle, [
                'Fecha',
                'Rifa',
                'Número',
                'Participante',
                'Teléfono',
                'Email',
                'Estado Anterior',
                'Estado Nuevo',
                'Tipo de Acción',
                'Monto',
                'Notas',
                'Usuario que Cambió'
            ]);

            // Datos
            foreach ($payments as $payment) {
                fputcsv($handle, [
                    $payment->created_at->format('d/m/Y H:i:s'),
                    $payment->raffle->name,
                    $payment->number->number,
                    $payment->participant->name ?? 'N/A',
                    $payment->participant->phone ?? 'N/A',
                    $payment->participant->email ?? 'N/A',
                    $payment->old_status ?? 'N/A',
                    $payment->new_status,
                    $payment->action_type_description,
                    $payment->amount ? number_format($payment->amount, 2) : 'N/A',
                    $payment->notes ?? 'N/A',
                    $payment->changedBy->name
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function editPayment(NumberStatusAudit $audit)
    {
        $audit->load(['number', 'raffle', 'participant', 'changedBy', 'paymentConfirmedBy']);
        return view('admin.audit.edit-payment', compact('audit'));
    }

    public function updatePayment(Request $request, NumberStatusAudit $audit)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'payment_evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'payment_confirmed' => 'boolean'
        ]);

        try {
            $data = [
                'amount' => $request->amount,
                'notes' => $request->notes,
            ];

            // Manejar evidencia de pago
            if ($request->hasFile('payment_evidence')) {
                $file = $request->file('payment_evidence');
                $filename = 'payment_evidence_' . $audit->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('payment_evidence', $filename, 'public');

                $data['payment_evidence_path'] = $path;
                $data['payment_evidence_type'] = $file->getClientOriginalExtension();
            }

            // Confirmar pago si se solicita
            if ($request->payment_confirmed) {
                $data['payment_confirmed'] = true;
                $data['payment_confirmed_at'] = now();
                $data['payment_confirmed_by'] = auth()->id();
            }

            $audit->update($data);

            $message = 'Pago actualizado correctamente';
            if ($request->payment_confirmed) {
                $message .= ' y confirmado';
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Error al actualizar pago: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al actualizar el pago: ' . $e->getMessage()]);
        }
    }

    public function confirmPayment(NumberStatusAudit $audit)
    {
        try {
            $audit->update([
                'payment_confirmed' => true,
                'payment_confirmed_at' => now(),
                'payment_confirmed_by' => auth()->id()
            ]);

            return back()->with('success', 'Pago confirmado correctamente');

        } catch (\Exception $e) {
            \Log::error('Error al confirmar pago: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al confirmar el pago: ' . $e->getMessage()]);
        }
    }

    public function downloadEvidence(NumberStatusAudit $audit)
    {
        if (!$audit->payment_evidence_path) {
            abort(404, 'No hay evidencia de pago disponible');
        }

        $filePath = storage_path('app/public/' . $audit->payment_evidence_path);

        if (!file_exists($filePath)) {
            abort(404, 'Archivo de evidencia no encontrado');
        }

        return response()->download($filePath);
    }
}
