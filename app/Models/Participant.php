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

    public function numbers()
    {
        return $this->hasMany(Number::class);
    }

    /**
     * Buscar participante existente por email o teléfono
     */
    public static function findExisting($email = null, $phone = null)
    {
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

        // Agregar reglas únicas solo si se está creando un nuevo participante
        if (!$participantId) {
            $rules['email'] .= '|unique:participants,email';
            $rules['phone'] .= '|unique:participants,phone';
        } else {
            $rules['email'] .= '|unique:participants,email,' . $participantId;
            $rules['phone'] .= '|unique:participants,phone,' . $participantId;
        }

        return $rules;
    }
}
