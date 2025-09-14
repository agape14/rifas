<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrawResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'raffle_id',
        'prize_id',
        'number',
        'participant_id',
        'prize_image',
        'drawn_at',
        'status'
    ];

    protected $casts = [
        'drawn_at' => 'datetime',
    ];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
