<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Number extends Model
{
    use HasFactory;

    protected $fillable = [
        'raffle_id',
        'number',
        'participant_id',
        'status',
        'price'
    ];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
