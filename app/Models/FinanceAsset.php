<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinanceAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'category_id',
        'asset_number',
        'asset_name',
        'description',
        'asset_type',
        'asset_category',
        'purchase_cost',
        'purchase_date_bs',
        'fiscal_year_bs',
        'vendor_name',
        'invoice_number',
        'depreciation_method',
        'useful_life_years',
        'useful_life_months',
        'salvage_value',
        'depreciation_rate',
        'accumulated_depreciation',
        'book_value',
        'depreciation_start_date_bs',
        'location',
        'assigned_to',
        'serial_number',
        'barcode',
        'status',
        'disposal_date_bs',
        'disposal_value',
        'disposal_reason',
        'last_maintenance_date_bs',
        'next_maintenance_date_bs',
        'total_maintenance_cost',
        'transferred_to_company_id',
        'transfer_date_bs',
        'document_path',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'book_value' => 'decimal:2',
        'disposal_value' => 'decimal:2',
        'total_maintenance_cost' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(FinanceCompany::class, 'company_id');
    }

    public function category()
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    public function depreciationRecords()
    {
        return $this->hasMany(FinanceAssetDepreciation::class, 'finance_asset_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function calculateMonthlyDepreciation()
    {
        $depreciableAmount = $this->purchase_cost - $this->salvage_value;
        $totalMonths = ($this->useful_life_years * 12) + ($this->useful_life_months ?? 0);

        if ($this->depreciation_method === 'straight_line') {
            return $totalMonths > 0 ? $depreciableAmount / $totalMonths : 0;
        }

        return 0;
    }

    public function updateBookValue()
    {
        $this->book_value = $this->purchase_cost - $this->accumulated_depreciation;
        $this->save();
    }
}
