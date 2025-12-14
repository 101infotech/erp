<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrmDepartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
    ];

    /**
     * Get the company that owns the department
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(HrmCompany::class, 'company_id');
    }

    /**
     * Get employees in this department
     */
    public function employees(): HasMany
    {
        return $this->hasMany(HrmEmployee::class, 'department_id');
    }
}
