<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumberStatusAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'number_id',
        'raffle_id',
        'participant_id',
        'old_status',
        'new_status',
        'action_type',
        'amount',
        'notes',
        'payment_evidence_path',
        'payment_evidence_type',
        'payment_confirmed',
        'payment_confirmed_at',
        'payment_confirmed_by',
        'changed_by',
        'bulk_data'
    ];

    protected $casts = [
        'bulk_data' => 'array',
        'amount' => 'decimal:2',
        'payment_confirmed' => 'boolean',
        'payment_confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones
    public function number(): BelongsTo
    {
        return $this->belongsTo(Number::class);
    }

    public function raffle(): BelongsTo
    {
        return $this->belongsTo(Raffle::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function paymentConfirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_confirmed_by');
    }

    // Scopes
    public function scopeForRaffle($query, $raffleId)
    {
        return $query->where('raffle_id', $raffleId);
    }

    public function scopePaidStatus($query)
    {
        return $query->where('new_status', 'pagado');
    }

    public function scopeBulkActions($query)
    {
        return $query->whereIn('action_type', ['bulk_mark_paid', 'bulk_release']);
    }

    public function scopeIndividualActions($query)
    {
        return $query->where('action_type', 'individual');
    }

    public function scopeWithAmount($query)
    {
        return $query->whereNotNull('amount');
    }

    // Métodos de utilidad
    public function getStatusChangeDescriptionAttribute(): string
    {
        $oldStatus = $this->old_status ? ucfirst($this->old_status) : 'Sin estado';
        $newStatus = ucfirst($this->new_status);

        return "De {$oldStatus} a {$newStatus}";
    }

    public function getActionTypeDescriptionAttribute(): string
    {
        return match($this->action_type) {
            'individual' => 'Cambio Individual',
            'bulk_mark_paid' => 'Marcado Masivo como Pagado',
            'bulk_release' => 'Liberación Masiva',
            default => ucfirst($this->action_type)
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->amount ? 'S/.' . number_format($this->amount, 2) : 'N/A';
    }

    public function getPaymentStatusAttribute(): string
    {
        if ($this->payment_confirmed) {
            return 'Confirmado';
        } elseif ($this->payment_evidence_path) {
            return 'Pendiente de Confirmación';
        } else {
            return 'Sin Evidencia';
        }
    }

    public function getPaymentStatusColorAttribute(): string
    {
        if ($this->payment_confirmed) {
            return 'green';
        } elseif ($this->payment_evidence_path) {
            return 'yellow';
        } else {
            return 'red';
        }
    }

    public function hasPaymentEvidence(): bool
    {
        return !empty($this->payment_evidence_path);
    }

    public function isPaymentConfirmed(): bool
    {
        return $this->payment_confirmed;
    }
}
