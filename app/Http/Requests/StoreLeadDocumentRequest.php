<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type' => 'required|in:photo,design,contract,quotation,report,invoice,proposal,other',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif|max:5120',
            'description' => 'nullable|string|max:500',
            'document_date' => 'nullable|date|before_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'document_type.required' => 'Document type is required',
            'document_type.in' => 'Invalid document type',
            'document_file.required' => 'Document file is required',
            'document_file.file' => 'File must be a valid file',
            'document_file.mimes' => 'File must be a PDF, Word, Excel, or Image file',
            'document_file.max' => 'File size must not exceed 5MB',
            'document_date.date' => 'Document date must be a valid date',
            'document_date.before_or_equal' => 'Document date cannot be in the future',
        ];
    }
}
