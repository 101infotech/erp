<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDocumentRequest;
use App\Http\Requests\Api\UpdateDocumentRequest;
use App\Models\ServiceLead;
use App\Models\LeadDocument;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class LeadDocumentController extends Controller
{
    /**
     * Get all documents for a lead
     */
    public function index(ServiceLead $lead, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $documents = $lead->documents()
                ->with('uploadedBy')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $documents->items(),
                'pagination' => [
                    'total' => $documents->total(),
                    'per_page' => $documents->perPage(),
                    'current_page' => $documents->currentPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve documents',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Upload a new document for a lead
     */
    public function store(ServiceLead $lead, StoreDocumentRequest $request): JsonResponse
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file provided',
                ], 400);
            }

            $file = $request->file('file');
            $documentType = $request->get('document_type', 'other');

            // Store file
            $path = $file->store("leads/{$lead->id}/{$documentType}", 'public');

            // Create document record
            $document = $lead->documents()->create([
                'document_type' => $documentType,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'description' => $request->get('description'),
                'uploaded_by_id' => auth()->id(),
            ]);

            // Update lead last activity
            $lead->update(['last_activity_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'data' => $document->load('uploadedBy'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display a specific document
     */
    public function show(ServiceLead $lead, LeadDocument $document): JsonResponse
    {
        try {
            if ($document->lead_id !== $lead->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document does not belong to this lead',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $document->load('uploadedBy'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve document',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update document details (description, type)
     */
    public function update(ServiceLead $lead, LeadDocument $document, UpdateDocumentRequest $request): JsonResponse
    {
        try {
            if ($document->lead_id !== $lead->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document does not belong to this lead',
                ], 404);
            }

            $document->update($request->validated());

            // Update lead last activity
            $lead->update(['last_activity_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Document updated successfully',
                'data' => $document->load('uploadedBy'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update document',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete a document
     */
    public function destroy(ServiceLead $lead, LeadDocument $document): JsonResponse
    {
        try {
            if ($document->lead_id !== $lead->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document does not belong to this lead',
                ], 404);
            }

            // Delete physical file
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Soft delete document record
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Restore a soft-deleted document
     */
    public function restore(ServiceLead $lead, $documentId): JsonResponse
    {
        try {
            $document = LeadDocument::onlyTrashed()
                ->where('id', $documentId)
                ->where('lead_id', $lead->id)
                ->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found',
                ], 404);
            }

            $document->restore();

            return response()->json([
                'success' => true,
                'message' => 'Document restored successfully',
                'data' => $document->load('uploadedBy'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore document',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get documents by type
     */
    public function byType(ServiceLead $lead, Request $request): JsonResponse
    {
        try {
            $request->validate(['type' => 'required|in:photo,design,contract,quotation,report,other']);

            $perPage = $request->get('per_page', 15);
            $documents = $lead->documents()
                ->where('document_type', $request->type)
                ->with('uploadedBy')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $documents->items(),
                'pagination' => [
                    'total' => $documents->total(),
                    'per_page' => $documents->perPage(),
                    'current_page' => $documents->currentPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve documents',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get document download URL
     */
    public function download(ServiceLead $lead, LeadDocument $document)
    {
        try {
            if ($document->lead_id !== $lead->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document does not belong to this lead',
                ], 404);
            }

            if (!Storage::disk('public')->exists($document->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found',
                ], 404);
            }

            return Storage::disk('public')->download($document->file_path, $document->file_name);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download document',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
