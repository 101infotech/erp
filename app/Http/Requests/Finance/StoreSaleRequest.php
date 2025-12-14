<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'sale_date_bs' => ['required', 'string', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'customer_id' => ['nullable', 'integer', 'exists:finance_customers,id'],
            'customer_name' => ['required_without:customer_id', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'vat_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['required', 'string', Rule::in(['pending', 'partial', 'paid'])],
            'payment_method' => ['required_if:payment_status,paid', 'nullable', 'string', Rule::in(['cash', 'bank_transfer', 'cheque', 'card', 'online', 'mobile_banking'])],
            'payment_date_bs' => ['required_if:payment_status,paid', 'nullable', 'string', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'invoice_number' => ['nullable', 'string', 'max:50', Rule::unique('finance_sales')->where('company_id', $this->company_id)],
            'description' => ['nullable', 'string', 'max:500'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'debit_account_id' => ['required_if:payment_status,paid', 'nullable', 'integer', 'exists:finance_accounts,id'],
            'credit_account_id' => ['required_if:payment_status,paid', 'nullable', 'integer', 'exists:finance_accounts,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'sale_date_bs.regex' => 'Sale date must be in BS format (YYYY-MM-DD)',
            'payment_date_bs.regex' => 'Payment date must be in BS format (YYYY-MM-DD)',
            'invoice_number.unique' => 'Invoice number already exists for this company',
            'document.max' => 'Document size must not exceed 5MB',
        ];
    }
}
