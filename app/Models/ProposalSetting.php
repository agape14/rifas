<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalSetting extends Model
{
    protected $fillable = ['features', 'pricing'];
    protected $casts = [
        'features' => 'array',
        'pricing' => 'array',
    ];
}
