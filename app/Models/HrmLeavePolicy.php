<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrmLeavePolicy extends Model
{
    protected $fillable = [
        'company_id',
        'policy_name',
        'leave_type',
        'gender_restriction',
        'annual_quota',
        'carry_forward_allowed',
        'max_carry_forward',
        'requires_approval',
        'is_active',
    ];

    protected $casts = [
        'carry_forward_allowed' => 'boolean',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(HrmCompany::class, 'company_id');
    }
}
