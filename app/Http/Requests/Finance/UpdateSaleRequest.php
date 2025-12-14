<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $saleId = $this->route('sale');

        return [
            'sale_date_bs' => ['sometimes', 'string', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'customer_id' => ['nullable', 'integer', 'exists:finance_customers,id'],
            'customer_name' => ['sometimes', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'total_amount' => ['sometimes', 'numeric', 'min:0.01'],
            'vat_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['sometimes', 'string', Rule::in(['pending', 'partial', 'paid'])],
            'payment_method' => ['nullable', 'string', Rule::in(['cash', 'bank_transfer', 'cheque', 'card', 'online', 'mobile_banking'])],
            'payment_date_bs' => ['nullable', 'string', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
            'invoice_number' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'sale_date_bs.regex' => 'Sale date must be in BS format (YYYY-MM-DD)',
            'payment_date_bs.regex' => 'Payment date must be in BS format (YYYY-MM-DD)',
            'document.max' => 'Document size must not exceed 5MB',
        ];
    }
}
