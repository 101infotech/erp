<?php

namespace App\Http\Resources\Finance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancePurchaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'company' => [
                'id' => $this->company->id ?? null,
                'name' => $this->company->name ?? null,
            ],
            'purchase_number' => $this->purchase_number,
            'purchase_date_bs' => $this->purchase_date_bs,
            'fiscal_year_bs' => $this->fiscal_year_bs,
            'vendor' => $this->vendor ? [
                'id' => $this->vendor->id,
                'name' => $this->vendor->name,
                'email' => $this->vendor->email,
                'phone' => $this->vendor->phone,
                'pan' => $this->vendor->pan,
            ] : [
                'name' => $this->vendor_name,
                'email' => $this->vendor_email,
                'phone' => $this->vendor_phone,
                'pan' => $this->vendor_pan,
            ],
            'total_amount' => number_format($this->total_amount, 2, '.', ''),
            'taxable_amount' => number_format($this->taxable_amount ?? 0, 2, '.', ''),
            'vat_amount' => number_format($this->vat_amount ?? 0, 2, '.', ''),
            'tds_amount' => number_format($this->tds_amount ?? 0, 2, '.', ''),
            'tds_percentage' => number_format($this->tds_percentage ?? 0, 2, '.', ''),
            'discount_amount' => number_format($this->discount_amount ?? 0, 2, '.', ''),
            'net_amount' => number_format($this->net_amount, 2, '.', ''),
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'payment_date_bs' => $this->payment_date_bs,
            'bill_number' => $this->bill_number,
            'description' => $this->description,
            'document_path' => $this->document_path,
            'transaction' => $this->transaction ? [
                'id' => $this->transaction->id,
                'amount' => number_format($this->transaction->amount, 2, '.', ''),
                'status' => $this->transaction->status,
            ] : null,
            'created_by' => [
                'id' => $this->createdBy->id ?? null,
                'name' => $this->createdBy->name ?? null,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
