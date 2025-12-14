<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreSaleRequest;
use App\Http\Requests\Finance\UpdateSaleRequest;
use App\Http\Resources\Finance\FinanceSaleResource;
use App\Models\FinanceSale;
use App\Services\Finance\FinanceSaleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FinanceSaleController extends Controller
{
    protected FinanceSaleService $saleService;

    public function __construct(FinanceSaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * Display a listing of sales
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = FinanceSale::with(['company', 'customer', 'transaction', 'createdBy']);

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

            // Filter by customer
            if ($request->has('customer_id')) {
                $query->where('customer_id', $request->input('customer_id'));
            }

            // Date range filter
            if ($request->has('from_date') && $request->has('to_date')) {
                $query->whereBetween('sale_date_bs', [
                    $request->input('from_date'),
                    $request->input('to_date')
                ]);
            }

            $sales = $query->latest('sale_date_bs')->paginate(50);

            return response()->json([
                'success' => true,
                'data' => FinanceSaleResource::collection($sales),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sales',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created sale
     */
    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            $sale = $this->saleService->createSale(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Sale created successfully',
                'data' => new FinanceSaleResource($sale),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sale',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified sale
     */
    public function show(FinanceSale $sale): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new FinanceSaleResource($sale->load([
                    'company',
                    'customer',
                    'transaction',
                    'createdBy'
                ])),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sale',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified sale
     */
    public function update(UpdateSaleRequest $request, FinanceSale $sale): JsonResponse
    {
        try {
            $updated = $this->saleService->updateSale($sale, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Sale updated successfully',
                'data' => new FinanceSaleResource($updated),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sale',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified sale
     */
    public function destroy(FinanceSale $sale): JsonResponse
    {
        try {
            if ($sale->payment_status === 'paid' && $sale->transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete paid sale with transaction. Reverse the transaction first.',
                ], 400);
            }

            $sale->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sale deleted successfully',
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sale',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Record payment for a sale
     */
    public function recordPayment(Request $request, FinanceSale $sale): JsonResponse
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'payment_date_bs' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
                'payment_method' => 'required|string|in:cash,bank_transfer,cheque,card,online,mobile_banking',
                'debit_account_id' => 'required|integer|exists:finance_accounts,id',
                'credit_account_id' => 'required|integer|exists:finance_accounts,id',
            ]);

            $updated = $this->saleService->recordPayment(
                $sale,
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
                'data' => new FinanceSaleResource($updated),
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
     * Get sales summary for a period
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
                'from_date' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
                'to_date' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/',
            ]);

            $summary = $this->saleService->getSalesSummary(
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
                'message' => 'Failed to get sales summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer-wise sales
     */
    public function customerSales(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            ]);

            $sales = $this->saleService->getCustomerSales(
                $request->input('company_id'),
                $request->input('fiscal_year')
            );

            return response()->json([
                'success' => true,
                'data' => $sales,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get customer sales',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get monthly sales trends
     */
    public function monthlyTrends(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
                'fiscal_year' => 'required|string|regex:/^\d{4}$/',
            ]);

            $trends = $this->saleService->getMonthlySalesTrends(
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
}
