<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class FinancePurchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'purchase_number',
        'purchase_date_bs',
        'vendor_id',
        'vendor_name',
        'vendor_pan',
        'vendor_address',
        'bill_number',
        'total_amount',
        'vat_amount',
        'tds_amount',
        'tds_percentage',
        'taxable_amount',
        'discount_amount',
        'net_amount',
        'payment_status',
        'payment_method',
        'payment_date_bs',
        'description',
        'fiscal_year_bs',
        'document_path',
        'created_by_user_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'tds_amount' => 'decimal:2',
        'tds_percentage' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(FinanceVendor::class, 'vendor_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by_user_id');
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(FinanceTransaction::class, 'reference');
    }

    // Scopes
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByFiscalYear($query, $fiscalYear)
    {
        return $query->where('fiscal_year_bs', $fiscalYear);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->whereIn('payment_status', ['pending', 'partial']);
    }

    // Helper Methods
    public static function generatePurchaseNumber(int $companyId): string
    {
        $prefix = 'PUR';
        $date = now()->format('Ymd');
        $lastPurchase = self::where('company_id', $companyId)
            ->whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastPurchase
            ? ((int) substr($lastPurchase->purchase_number, -4)) + 1
            : 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }
}
