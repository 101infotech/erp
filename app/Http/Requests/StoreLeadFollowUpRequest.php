<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadFollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'follow_up_type' => 'required|in:call,visit,whatsapp,email,sms,meeting,other',
            'follow_up_date' => 'required|date|after_or_equal:today',
            'notes' => 'required|string|min:10',
            'outcome' => 'nullable|string|max:255',
            'follow_up_reminder' => 'nullable|boolean',
            'reminder_date' => 'nullable|date|after:follow_up_date',
        ];
    }

    public function messages(): array
    {
        return [
            'follow_up_type.required' => 'Follow-up type is required',
            'follow_up_type.in' => 'Follow-up type must be call, visit, whatsapp, email, sms, meeting, or other',
            'follow_up_date.required' => 'Follow-up date is required',
            'follow_up_date.date' => 'Follow-up date must be a valid date',
            'follow_up_date.after_or_equal' => 'Follow-up date must be today or in the future',
            'notes.required' => 'Notes are required',
            'notes.min' => 'Notes must be at least 10 characters',
            'reminder_date.after' => 'Reminder date must be after follow-up date',
        ];
    }
}
