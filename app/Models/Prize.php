<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    use HasFactory;

    protected $fillable = [
        'raffle_id',
        'name',
        'image',
        'description',
        'order'
    ];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }
}
