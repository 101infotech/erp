<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'service_type' => 'required|string|max:100',
            'service_description' => 'required|string',
            'lead_source' => 'nullable|string|max:100',
            'priority' => 'nullable|in:urgent,high,medium,low',
            'quoted_amount' => 'nullable|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'lead_owner_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required',
            'contact_person.required' => 'Contact person name is required',
            'email.required' => 'Email address is required',
            'email.email' => 'Email address must be valid',
            'phone.required' => 'Phone number is required',
            'service_type.required' => 'Service type is required',
            'service_description.required' => 'Service description is required',
            'priority.in' => 'Priority must be urgent, high, medium, or low',
            'quoted_amount.numeric' => 'Quoted amount must be a valid number',
            'advance_amount.numeric' => 'Advance amount must be a valid number',
            'lead_owner_id.exists' => 'The selected user does not exist',
        ];
    }
}
