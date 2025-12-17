<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrmPayrollRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'period_start_bs',
        'period_end_bs',
        'period_start_ad',
        'period_end_ad',
        'month_total_days',
        'standard_working_hours_per_day',
        'total_hours_worked',
        'total_working_hours_required',
        'total_working_hours_missing',
        'total_days_worked',
        'overtime_hours',
        'absent_days',
        'unpaid_leave_days',
        'paid_leave_days_used',
        'verbal_leave_days',
        'hourly_deduction_suggested',
        'hourly_deduction_amount',
        'hourly_deduction_approved',
        'basic_salary',
        'per_day_rate',
        'total_payable_days',
        'overtime_payment',
        'allowances',
        'allowances_total',
        'gross_salary',
        'tax_amount',
        'tax_overridden',
        'tax_override_reason',
        'deductions',
        'deductions_total',
        'unpaid_leave_deduction',
        'advance_payment',
        'advance_payment_details',
        'net_salary',
        'status',
        'approved_by',
        'approved_by_name',
        'approved_at',
        'paid_at',
        'payment_method',
        'transaction_reference',
        'payslip_pdf_path',
        'anomalies',
        'anomalies_reviewed',
        'notes',
        'sent_at',
        'sent_by',
        'sent_by_name',
    ];

    protected $casts = [
        'allowances' => 'array',
        'deductions' => 'array',
        'anomalies' => 'array',
        'advance_payment_details' => 'array',
        'tax_overridden' => 'boolean',
        'anomalies_reviewed' => 'boolean',
        'hourly_deduction_approved' => 'boolean',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'sent_at' => 'datetime',
        'month_total_days' => 'integer',
        'paid_leave_days_used' => 'integer',
        'verbal_leave_days' => 'integer',
        'standard_working_hours_per_day' => 'decimal:2',
        'total_working_hours_required' => 'decimal:2',
        'total_working_hours_missing' => 'decimal:2',
        'hourly_deduction_suggested' => 'decimal:2',
        'hourly_deduction_amount' => 'decimal:2',
        'advance_payment' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrmEmployee::class, 'employee_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Check if payroll dates collide with existing records for given employees
     * 
     * @param array $employeeIds
     * @param string $periodStartAd AD date format
     * @param string $periodEndAd AD date format
     * @param int|null $excludePayrollId Exclude specific payroll ID from check (for updates)
     * @return array ['has_collision' => bool, 'collisions' => array]
     */
    public static function checkDateCollisions(
        array $employeeIds,
        string $periodStartAd,
        string $periodEndAd,
        ?int $excludePayrollId = null
    ): array {
        $collisions = [];
        $hasCollision = false;

        foreach ($employeeIds as $employeeId) {
            $query = static::where('employee_id', $employeeId)
                ->where(function ($q) use ($periodStartAd, $periodEndAd) {
                    // Check for any overlap:
                    // New period starts during existing period
                    $q->where(function ($q2) use ($periodStartAd) {
                        $q2->whereRaw('? BETWEEN period_start_ad AND period_end_ad', [$periodStartAd]);
                    })
                        // New period ends during existing period
                        ->orWhere(function ($q2) use ($periodEndAd) {
                            $q2->whereRaw('? BETWEEN period_start_ad AND period_end_ad', [$periodEndAd]);
                        })
                        // New period completely encompasses existing period
                        ->orWhere(function ($q2) use ($periodStartAd, $periodEndAd) {
                            $q2->where('period_start_ad', '>=', $periodStartAd)
                                ->where('period_end_ad', '<=', $periodEndAd);
                        });
                });

            if ($excludePayrollId) {
                $query->where('id', '!=', $excludePayrollId);
            }

            $existingPayrolls = $query->with('employee:id,name,code')->get();

            if ($existingPayrolls->isNotEmpty()) {
                $hasCollision = true;
                $collisions[] = [
                    'employee_id' => $employeeId,
                    'employee_name' => $existingPayrolls->first()->employee->name ?? 'Unknown',
                    'employee_code' => $existingPayrolls->first()->employee->code ?? '',
                    'existing_payrolls' => $existingPayrolls->map(function ($payroll) {
                        return [
                            'id' => $payroll->id,
                            'period_start_bs' => $payroll->period_start_bs,
                            'period_end_bs' => $payroll->period_end_bs,
                            'period_start_ad' => $payroll->period_start_ad,
                            'period_end_ad' => $payroll->period_end_ad,
                            'status' => $payroll->status,
                        ];
                    })->toArray(),
                ];
            }
        }

        return [
            'has_collision' => $hasCollision,
            'collisions' => $collisions,
        ];
    }
}
