<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\FinanceSale;
use App\Models\FinancePurchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FinanceDocumentController extends Controller
{
    /**
     * Download transaction document
     */
    public function downloadTransactionDocument(FinanceTransaction $transaction): BinaryFileResponse|JsonResponse
    {
        try {
            if (!$transaction->document_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No document found for this transaction',
                ], 404);
            }

            if (!Storage::disk('public')->exists($transaction->document_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document file not found',
                ], 404);
            }

            $path = Storage::disk('public')->path($transaction->document_path);
            return response()->download($path, basename($transaction->document_path));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download sale document
     */
    public function downloadSaleDocument(FinanceSale $sale): BinaryFileResponse|JsonResponse
    {
        try {
            if (!$sale->document_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No document found for this sale',
                ], 404);
            }

            if (!Storage::disk('public')->exists($sale->document_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document file not found',
                ], 404);
            }

            $path = Storage::disk('public')->path($sale->document_path);
            return response()->download($path, basename($sale->document_path));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download purchase document
     */
    public function downloadPurchaseDocument(FinancePurchase $purchase): BinaryFileResponse|JsonResponse
    {
        try {
            if (!$purchase->document_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No document found for this purchase',
                ], 404);
            }

            if (!Storage::disk('public')->exists($purchase->document_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document file not found',
                ], 404);
            }

            $path = Storage::disk('public')->path($purchase->document_path);
            return response()->download($path, basename($purchase->document_path));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download document',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
