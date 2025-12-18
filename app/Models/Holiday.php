<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'description',
        'is_company_wide',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_company_wide' => 'boolean',
        'is_active' => 'boolean',
    ];
}
