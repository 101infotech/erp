<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'transaction_date_bs' => ['required', 'string', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'transaction_type' => ['required', 'string', Rule::in(['income', 'expense', 'transfer', 'investment', 'withdrawal'])],
            'category_id' => ['nullable', 'integer', 'exists:finance_categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'debit_account_id' => ['required', 'integer', 'exists:finance_accounts,id', 'different:credit_account_id'],
            'credit_account_id' => ['required', 'integer', 'exists:finance_accounts,id'],
            'payment_method' => ['required', 'string', Rule::in(['cash', 'bank_transfer', 'cheque', 'card', 'online', 'mobile_banking'])],
            'description' => ['nullable', 'string', 'max:500'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'reference_type' => ['nullable', 'string'],
            'reference_id' => ['nullable', 'integer'],
            'from_holding' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'transaction_date_bs.regex' => 'Transaction date must be in BS format (YYYY-MM-DD)',
            'debit_account_id.different' => 'Debit and credit accounts must be different',
            'document.max' => 'Document size must not exceed 5MB',
        ];
    }
}
