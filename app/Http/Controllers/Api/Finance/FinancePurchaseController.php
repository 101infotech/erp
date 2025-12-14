<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StorePurchaseRequest;
use App\Http\Requests\Finance\UpdatePurchaseRequest;
use App\Http\Resources\Finance\FinancePurchaseResource;
use App\Models\FinancePurchase;
use App\Services\Finance\FinancePurchaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FinancePurchaseController extends Controller
{
    protected FinancePurchaseService $purchaseService;

    public function __construct(FinancePurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    /**
     * Display a listing of purchases
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = FinancePurchase::with(['company', 'vendor', 'transaction', 'createdBy']);

            // Filter by company
            if ($request->has('company_id')) {
                $query->byCompany($request->input('company_id'));
            }

            // Filter by fiscal year
            if ($request->has('fiscal_year')) {
                $query->byFiscalYear($request->input('fiscal_year'));
            }

            // Filter by payment status
            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->input('payment_status'));
            }

            // Filter by vendor
            if ($request->has('vendor_id')) {
                $query->where('vendor_id', $request->input('vendor_id'));
            }

            // Date range filter
            if ($request->has('from_date') && $request->has('to_date')) {
                $query->whereBetween('purchase_date_bs', [
                    $request->input('from_date'),
                    $request->input('to_date')
                ]);
            }

            $purchases = $query->latest('purchase_date_bs')->paginate(50);

            return response()->json([
                'success' => true,
                'data' => FinancePurchaseResource::collection($purchases),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch purchases',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created purchase
     */
    public function store(StorePurchaseRequest $request): JsonResponse
    {
        try {
            $purchase = $this->purchaseService->createPurchase(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Purchase created successfully',
                'data' => new FinancePurchaseResource($purchase),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create purchase',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified purchase
     */
    public function show(FinancePurchase $purchase): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new FinancePurchaseResource($purchase->load([
                    'company',
                    'vendor',
                    'transaction',
                    'createdBy'
                ])),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch purchase',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified purchase
     */
    public function update(UpdatePurchaseRequest $request, FinancePurchase $purchase): JsonResponse
    {
        try {
            $updated = $this->purchaseService->updatePurchase($purchase, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Purchase updated successfully',
                'data' => new FinancePurchaseResource($updated),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update purchase',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified purchase
     */
    public function destroy(FinancePurchase $purchase): JsonResponse
    {
        try {
            if ($purchase->payment_status === 'paid' && $purchase->transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete paid purchase with transaction. Reverse the transaction first.',
                ], 400);
            }

            $purchase->delete();

            return response()->json([
                'success' => true,
                'message' => 'Purchase deleted successfully',
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete purchase',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Record payment for a purchase
     */
    public function recordPayment(Request $request, FinancePurchase $purchase): JsonResponse
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'payment_date_bs' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
                'payment_method' => 'required|string|in:cash,bank_transfer,cheque,card,online,mobile_banking',
                'debit_account_id' => 'required|integer|exists:finance_accounts,id',
                'credit_account_id' => 'required|integer|exists:finance_accounts,id',
            ]);

            $updated = $this->purchaseService->recordPayment(
                $purchase,
                $request->input('amount'),
                $request->input('payment_date_bs'),
                $request->input('payment_method'),
                $request->input('debit_account_id'),
                $request->input('credit_account_id'),
                $request->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => new FinancePurchaseResource($updated),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get purchases summary for a period
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
                'from_date' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
                'to_date' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
            ]);

            $summary = $this->purchaseService->getPurchasesSummary(
                $request->input('company_id'),
                $request->input('from_date'),
                $request->input('to_date')
            );

            return response()->json([
                'success' => true,
                'data' => $summary,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get purchases summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get vendor-wise purchases
     */
    public function vendorPurchases(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            ]);

            $purchases = $this->purchaseService->getVendorPurchases(
                $request->input('company_id'),
                $request->input('fiscal_year')
            );

            return response()->json([
                'success' => true,
                'data' => $purchases,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get vendor purchases',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get monthly purchase trends
     */
    public function monthlyTrends(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            ]);

            $trends = $this->purchaseService->getMonthlyPurchaseTrends(
                $request->input('company_id'),
                $request->input('fiscal_year')
            );

            return response()->json([
                'success' => true,
                'data' => $trends,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get monthly trends',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get TDS summary for tax compliance
     */
    public function tdsSummary(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            ]);

            $summary = $this->purchaseService->getTdsSummary(
                $request->input('company_id'),
                $request->input('fiscal_year')
            );

            return response()->json([
                'success' => true,
                'data' => $summary,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get TDS summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
