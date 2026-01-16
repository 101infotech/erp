<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,cheque,bank_transfer,upi,credit_card,debit_card,other',
            'payment_date' => 'required|date|before_or_equal:today',
            'reference_number' => 'nullable|string|max:100|unique:lead_payments,reference_number',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Payment amount is required',
            'amount.numeric' => 'Payment amount must be a valid number',
            'amount.min' => 'Payment amount must be greater than 0',
            'payment_method.required' => 'Payment method is required',
            'payment_method.in' => 'Invalid payment method',
            'payment_date.required' => 'Payment date is required',
            'payment_date.date' => 'Payment date must be a valid date',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future',
            'reference_number.unique' => 'This reference number already exists',
        ];
    }
}
