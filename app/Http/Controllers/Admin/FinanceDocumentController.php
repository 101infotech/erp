<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceDocument;
use App\Models\FinanceCustomer;
use App\Models\FinanceVendor;
use App\Models\FinanceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinanceDocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'documentable_type' => 'required|string|in:customer,vendor',
            'documentable_id' => 'required|integer',
            'company_id' => 'required|exists:finance_companies,id',
            'type' => 'required|string|in:invoice,receipt,contract,agreement,pan_card,registration,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB max
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('finance/documents', $fileName, 'public');

        $documentableType = $request->documentable_type === 'customer'
            ? FinanceCustomer::class
            : FinanceVendor::class;

        $document = FinanceDocument::create([
            'documentable_type' => $documentableType,
            'documentable_id' => $request->documentable_id,
            'company_id' => $request->company_id,
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }

    public function download(FinanceDocument $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function destroy(FinanceDocument $document)
    {
        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }
}
