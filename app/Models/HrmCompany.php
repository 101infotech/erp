<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrmCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_email',
        'address',
        'finance_company_id',
    ];

    /**
     * Get departments for this company
     */
    public function departments(): HasMany
    {
        return $this->hasMany(HrmDepartment::class, 'company_id');
    }

    /**
     * Get employees for this company
     */
    public function employees(): HasMany
    {
        return $this->hasMany(HrmEmployee::class, 'company_id');
    }

    /**
     * Linked Finance company (1:1)
     */
    public function financeCompany(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'finance_company_id');
    }
}
