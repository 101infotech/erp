<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceAssetDepreciation extends Model
{
    protected $table = 'finance_asset_depreciation';

    protected $fillable = [
        'finance_asset_id',
        'company_id',
        'fiscal_year_bs',
        'fiscal_month_bs',
        'depreciation_date_bs',
        'opening_book_value',
        'depreciation_amount',
        'accumulated_depreciation',
        'closing_book_value',
        'calculation_method',
        'period_number',
        'calculation_notes',
        'is_adjustment',
        'adjustment_amount',
        'adjustment_reason',
        'status',
        'posted_date_bs',
        'posted_by',
    ];

    protected $casts = [
        'opening_book_value' => 'decimal:2',
        'depreciation_amount' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'closing_book_value' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'is_adjustment' => 'boolean',
    ];

    public function asset()
    {
        return $this->belongsTo(FinanceAsset::class, 'finance_asset_id');
    }

    public function company()
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
