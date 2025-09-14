<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'banner', 'description', 'draw_date', 'status', 'total_numbers', 'theme_color', 'terms_html',
        'organizer_name', 'organizer_id', 'organizer_address', 'organizer_contact', 'organizer_contact_email',
        'platform_name', 'broadcast_platform', 'privacy_url', 'claim_days', 'jurisdiction_city'
    ];

    public function numbers()
    {
        return $this->hasMany(Number::class);
    }

    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }

    public function drawResults()
    {
        return $this->hasMany(DrawResult::class);
    }

    /**
     * Renderizar términos y condiciones reemplazando variables {{ PLACEHOLDER }}
     */
    public function renderTermsHtml(): string
    {
        $raw = $this->terms_html ?? '';

        // Datos dinámicos
        $price = optional($this->numbers()->first())->price;
        $priceText = $price !== null ? number_format($price, 2) : '';

        $map = [
            '{{ raffle_name }}' => e($this->name),
            '{{ DRAW_DATE }}' => $this->draw_date ? \Carbon\Carbon::parse($this->draw_date)->format('d/m/Y') : '',
            '{{ PRICE }}' => e($priceText),
            '{{ last_updated }}' => now()->format('d/m/Y'),
            '{{ ORGANIZER_NAME }}' => e($this->organizer_name ?: config('raffle.organizer_name')),
            '{{ ORGANIZER_ID }}' => e($this->organizer_id ?: config('raffle.organizer_id')),
            '{{ ORGANIZER_ADDRESS }}' => e($this->organizer_address ?: config('raffle.organizer_address')),
            '{{ ORGANIZER_CONTACT }}' => e($this->organizer_contact ?: config('raffle.organizer_contact')),
            '{{ ORGANIZER_CONTACT_EMAIL }}' => e($this->organizer_contact_email ?: config('raffle.organizer_contact_email')),
            '{{ PLATFORM_NAME }}' => e($this->platform_name ?: config('raffle.platform_name')),
            '{{ BROADCAST_PLATFORM }}' => e($this->broadcast_platform ?: config('raffle.broadcast_platform')),
            '{{ PRIVACY_URL }}' => e($this->privacy_url ?: config('raffle.privacy_url')),
            '{{ CLAIM_DAYS }}' => e((string) ($this->claim_days ?: config('raffle.claim_days'))),
            '{{ JURISDICTION_CITY }}' => e($this->jurisdiction_city ?: config('raffle.jurisdiction_city')),
        ];

        // Reemplazo seguro de todas las llaves
        $html = strtr($raw, $map);

        return $html;
    }
}
