<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'banner', 'description', 'draw_date', 'status', 'total_numbers', 'theme_color'];

    public function numbers()
    {
        return $this->hasMany(Number::class);
    }

    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }

}
