<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'photo'
    ];

    /**
     * Always store phone normalized as +51999999999 (no spaces)
     */
    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = self::normalizePeruPhone($value);
    }

    /**
     * Accessor to render phone with spaces: +51 9XX XXX XXX
     */
    public function getPhoneFormattedAttribute(): ?string
    {
        return self::formatPeruPhone($this->attributes['phone'] ?? null);
    }

    /**
     * Normalize various inputs to +51999999999 (no spaces)
     */
    public static function normalizePeruPhone(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $value);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '51')) {
            $digits = substr($digits, 2);
        }
        $digits = ltrim($digits, '0');
        $digits = substr($digits, 0, 9);
        if ($digits === '') {
            return null;
        }
        return '+51' . $digits;
    }

    /**
     * Format +51999999999 to +51 9XX XXX XXX
     */
    public static function formatPeruPhone(?string $normalized): ?string
    {
        if (!$normalized) return null;
        $digits = preg_replace('/\D+/', '', $normalized);
        if (str_starts_with($digits, '51')) {
            $digits = substr($digits, 2);
        }
        $digits = substr($digits, 0, 9);
        if (strlen($digits) < 1) return '+51';
        $part1 = substr($digits, 0, 3);
        $part2 = substr($digits, 3, 3);
        $part3 = substr($digits, 6, 3);
        $out = '+51 ' . $part1;
        if ($part2 !== '') $out .= ' ' . $part2;
        if ($part3 !== '') $out .= ' ' . $part3;
        return $out;
    }

    public function numbers()
    {
        return $this->hasMany(Number::class);
    }

    /**
     * Buscar participante existente por email o teléfono
     */
    public static function findExisting($email = null, $phone = null)
    {
        $phone = self::normalizePeruPhone($phone);
        if ($email) {
            $participant = self::where('email', $email)->first();
            if ($participant) return $participant;
        }

        if ($phone) {
            $participant = self::where('phone', $phone)->first();
            if ($participant) return $participant;
        }

        return null;
    }

    /**
     * Reglas de validación para crear/actualizar participantes
     */
    public static function getValidationRules($participantId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        return $rules;
    }
}
