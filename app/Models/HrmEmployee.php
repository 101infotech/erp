<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrmEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'department_id',
        'jibble_person_id',
        'name',
        'email',
        'phone',
        'code',
        'position',
        'hire_date',
        'date_of_birth',
        'gender',
        'blood_group',
        'marital_status',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'avatar',
        'avatar_url',
        'status',
        // Salary fields
        'basic_salary_npr',
        'salary_amount',
        'salary_type',
        'hourly_rate_npr',
        'allowances',
        'allowances_amount',
        'deductions',
        // Contract fields
        'contract_type',
        'contract_start_date',
        'contract_end_date',
        'contract_document',
        'probation_end_date',
        'probation_period_months',
        'employment_status',
        'employment_type',
        'working_days_per_week',
        // Leave balances
        'paid_leave_annual',
        'annual_leave_balance',
        'paid_leave_sick',
        'sick_leave_balance',
        'paid_leave_casual',
        'casual_leave_balance',
        'period_leave_balance',
        // Tax & Banking
        'pan_number',
        'tax_regime',
        'bank_account_number',
        'bank_name',
        'bank_branch',
        'citizenship_number',
        'permanent_address',
        'temporary_address',
        'citizenship_document',
    ];

    protected $casts = [
        'hire_date' => 'date:Y-m-d',
        'date_of_birth' => 'date:Y-m-d',
        'contract_start_date' => 'date:Y-m-d',
        'contract_end_date' => 'date:Y-m-d',
        'allowances' => 'array',
        'deductions' => 'array',
        'basic_salary_npr' => 'decimal:2',
        'salary_amount' => 'decimal:2',
        'hourly_rate_npr' => 'decimal:2',
        'allowances_amount' => 'decimal:2',
        'paid_leave_annual' => 'decimal:1',
        'paid_leave_sick' => 'decimal:1',
        'paid_leave_casual' => 'decimal:1',
        'sick_leave_balance' => 'decimal:1',
        'casual_leave_balance' => 'decimal:1',
        'annual_leave_balance' => 'decimal:1',
        'period_leave_balance' => 'decimal:1',
        'working_days_per_week' => 'integer',
        'probation_period_months' => 'integer',
    ];

    /**
     * Get the user that owns the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor for full_name to use the name column
     */
    public function getFullNameAttribute(): ?string
    {
        return $this->attributes['name'] ?? null;
    }

    /**
     * Mutator for full_name to set the name column
     */
    public function setFullNameAttribute(?string $value): void
    {
        $this->attributes['name'] = $value;
    }

    /**
     * Get the company that owns the employee
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(HrmCompany::class, 'company_id');
    }

    /**
     * Get the department that owns the employee
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(HrmDepartment::class, 'department_id');
    }

    /**
     * Get attendance records for this employee
     */
    public function attendanceDays(): HasMany
    {
        return $this->hasMany(HrmAttendanceDay::class, 'employee_id');
    }

    /**
     * Get payroll records for this employee
     */
    public function payrollRecords(): HasMany
    {
        return $this->hasMany(HrmPayrollRecord::class, 'employee_id');
    }

    /**
     * Get leave requests for this employee
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(HrmLeaveRequest::class, 'employee_id');
    }

    /**
     * Get attendance anomalies for this employee
     */
    public function attendanceAnomalies(): HasMany
    {
        return $this->hasMany(HrmAttendanceAnomaly::class, 'employee_id');
    }

    /**
     * Get resource requests for this employee
     */
    public function resourceRequests(): HasMany
    {
        return $this->hasMany(HrmResourceRequest::class, 'employee_id');
    }

    /**
     * Get expense claims for this employee
     */
    public function expenseClaims(): HasMany
    {
        return $this->hasMany(HrmExpenseClaim::class, 'employee_id');
    }

    /**
     * Get contract expiry days (negative if expired, positive if future, null if permanent)
     * Note: Converts BS date to AD for calculation
     */
    public function getContractExpiryDaysAttribute(): ?int
    {
        if (!$this->contract_end_date) {
            return null;
        }

        $calendarService = app(\App\Services\NepalCalendarService::class);
        $adDate = $calendarService->bsToAd($this->contract_end_date);

        if (!$adDate) {
            return null;
        }

        return now()->diffInDays($adDate, false);
    }

    /**
     * Get formatted hire date in Nepali
     */
    public function getHireDateFormattedAttribute(): ?string
    {
        return $this->hire_date ? format_nepali_date($this->hire_date, 'j F Y') : null;
    }

    /**
     * Get formatted date of birth in Nepali
     */
    public function getDateOfBirthFormattedAttribute(): ?string
    {
        return $this->date_of_birth ? format_nepali_date($this->date_of_birth, 'j F Y') : null;
    }

    /**
     * Get total paid leave quota days (sum of all leave types)
     */
    public function getPaidLeaveQuotaDaysAttribute(): ?int
    {
        $annual = $this->paid_leave_annual ?? 0;
        $sick = $this->paid_leave_sick ?? 0;
        $casual = $this->paid_leave_casual ?? 0;

        if ($annual > 0 || $sick > 0 || $casual > 0) {
            return (int) ($annual + $sick + $casual);
        }

        return null;
    }

    /**
     * Get total paid leave used days (computed from balances)
     */
    public function getPaidLeaveUsedDaysAttribute(): ?int
    {
        $quotaDays = $this->paid_leave_quota_days;
        if ($quotaDays === null) {
            return null;
        }

        $annualUsed = max(0, ($this->paid_leave_annual ?? 0) - ($this->annual_leave_balance ?? 0));
        $sickUsed = max(0, ($this->paid_leave_sick ?? 0) - ($this->sick_leave_balance ?? 0));
        $casualUsed = max(0, ($this->paid_leave_casual ?? 0) - ($this->casual_leave_balance ?? 0));

        return (int) ($annualUsed + $sickUsed + $casualUsed);
    }

    /**
     * Get formatted contract start date in Nepali
     */
    public function getContractStartDateFormattedAttribute(): ?string
    {
        return $this->contract_start_date ? format_nepali_date($this->contract_start_date, 'j F Y') : null;
    }

    /**
     * Get formatted contract end date in Nepali
     */
    public function getContractEndDateFormattedAttribute(): ?string
    {
        return $this->contract_end_date ? format_nepali_date($this->contract_end_date, 'j F Y') : null;
    }

    /**
     * Check if contract is expiring soon (within 30 days)
     */
    public function isContractExpiringSoon(): bool
    {
        $days = $this->contract_expiry_days;
        return $days !== null && $days > 0 && $days <= 30;
    }

    /**
     * Check if employee is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Scope to get only active employees
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get employees by company
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to get employees by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
