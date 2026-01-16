<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'sometimes|required|string|max:255',
            'contact_person' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'service_type' => 'sometimes|required|string|max:100',
            'service_description' => 'sometimes|required|string',
            'lead_source' => 'nullable|string|max:100',
            'lead_stage_id' => 'nullable|exists:lead_stages,id',
            'priority' => 'nullable|in:urgent,high,medium,low',
            'quoted_amount' => 'nullable|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'lead_owner_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'closure_reason' => 'nullable|string|max:255',
            'closed_at' => 'nullable|date',
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
            'lead_stage_id.exists' => 'The selected stage does not exist',
            'priority.in' => 'Priority must be urgent, high, medium, or low',
            'lead_owner_id.exists' => 'The selected user does not exist',
        ];
    }
}
