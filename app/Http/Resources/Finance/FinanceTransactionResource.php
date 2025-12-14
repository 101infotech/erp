<?php

namespace App\Http\Resources\Finance;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceTransactionResource extends JsonResource
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
            'transaction_date_bs' => $this->transaction_date_bs,
            'fiscal_year_bs' => $this->fiscal_year_bs,
            'fiscal_month_bs' => $this->fiscal_month_bs,
            'transaction_type' => $this->transaction_type,
            'category' => [
                'id' => $this->category->id ?? null,
                'name' => $this->category->name ?? null,
                'type' => $this->category->type ?? null,
            ],
            'amount' => number_format($this->amount, 2, '.', ''),
            'debit_account' => [
                'id' => $this->debitAccount->id ?? null,
                'name' => $this->debitAccount->name ?? null,
                'account_code' => $this->debitAccount->account_code ?? null,
                'account_type' => $this->debitAccount->account_type ?? null,
            ],
            'credit_account' => [
                'id' => $this->creditAccount->id ?? null,
                'name' => $this->creditAccount->name ?? null,
                'account_code' => $this->creditAccount->account_code ?? null,
                'account_type' => $this->creditAccount->account_type ?? null,
            ],
            'payment_method' => $this->payment_method,
            'description' => $this->description,
            'document_path' => $this->document_path,
            'status' => $this->status,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'from_holding' => $this->from_holding,
            'created_by' => [
                'id' => $this->createdBy->id ?? null,
                'name' => $this->createdBy->name ?? null,
            ],
            'approved_by' => $this->approvedBy ? [
                'id' => $this->approvedBy->id,
                'name' => $this->approvedBy->name,
            ] : null,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
