<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StoreTransactionRequest;
use App\Http\Requests\Finance\UpdateTransactionRequest;
use App\Http\Requests\Finance\ApproveTransactionRequest;
use App\Http\Resources\Finance\FinanceTransactionResource;
use App\Models\FinanceTransaction;
use App\Services\Finance\FinanceTransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class FinanceTransactionController extends Controller
{
    protected FinanceTransactionService $transactionService;

    public function __construct(FinanceTransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of transactions with filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $companyId = $request->input('company_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            // Provide safe defaults if dates are not provided
            if (is_null($fromDate) || is_null($toDate)) {
                // default to last 30 days
                $toDate = $toDate ?? now()->format('Y-m-d');
                $fromDate = $fromDate ?? now()->subDays(30)->format('Y-m-d');
            }

            $filters = [
                'transaction_type' => $request->input('transaction_type'),
                'category_id' => $request->input('category_id'),
                'status' => $request->input('status'),
                'payment_method' => $request->input('payment_method'),
                'from_holding' => $request->input('from_holding'),
            ];

            // Remove null filters
            $filters = array_filter($filters, function ($value) {
                return !is_null($value);
            });

            $transactions = $this->transactionService->getTransactionsByPeriod(
                $companyId,
                $fromDate,
                $toDate,
                $filters
            );

            return response()->json([
                'success' => true,
                'data' => FinanceTransactionResource::collection($transactions->paginate(50)),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transactions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created transaction
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        try {
            $transaction = $this->transactionService->createTransaction(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => new FinanceTransactionResource($transaction),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified transaction
     */
    public function show(FinanceTransaction $transaction): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => new FinanceTransactionResource($transaction->load([
                    'company',
                    'category',
                    'debitAccount',
                    'creditAccount',
                    'createdBy',
                    'approvedBy'
                ])),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transaction',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified transaction
     */
    public function update(UpdateTransactionRequest $request, FinanceTransaction $transaction): JsonResponse
    {
        try {
            $updated = $this->transactionService->updateTransaction(
                $transaction,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully',
                'data' => new FinanceTransactionResource($updated),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified transaction
     */
    public function destroy(FinanceTransaction $transaction): JsonResponse
    {
        try {
            if ($transaction->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete completed transaction. Use reverse instead.',
                ], 400);
            }

            $transaction->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully',
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Approve a transaction
     */
    public function approve(ApproveTransactionRequest $request, FinanceTransaction $transaction): JsonResponse
    {
        try {
            $approved = $this->transactionService->approveTransaction(
                $transaction,
                $request->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaction approved successfully',
                'data' => new FinanceTransactionResource($approved),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve transaction',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Complete a transaction
     */
    public function complete(FinanceTransaction $transaction): JsonResponse
    {
        try {
            $completed = $this->transactionService->completeTransaction($transaction);

            return response()->json([
                'success' => true,
                'message' => 'Transaction completed successfully',
                'data' => new FinanceTransactionResource($completed),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete transaction',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Cancel a transaction
     */
    public function cancel(Request $request, FinanceTransaction $transaction): JsonResponse
    {
        try {
            $reason = $request->input('reason', 'Cancelled by user');
            $cancelled = $this->transactionService->cancelTransaction($transaction, $reason);

            return response()->json([
                'success' => true,
                'message' => 'Transaction cancelled successfully',
                'data' => new FinanceTransactionResource($cancelled),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel transaction',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Reverse a completed transaction
     */
    public function reverse(Request $request, FinanceTransaction $transaction): JsonResponse
    {
        try {
            $reason = $request->input('reason', 'Reversal transaction');
            $reversal = $this->transactionService->reverseTransaction(
                $transaction,
                $request->user(),
                $reason
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaction reversed successfully',
                'data' => new FinanceTransactionResource($reversal),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reverse transaction',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get period totals
     */
    public function periodTotals(Request $request): JsonResponse
    {
        try {
            $companyId = $request->input('company_id');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $totals = $this->transactionService->calculatePeriodTotals(
                $companyId,
                $fromDate,
                $toDate
            );

            return response()->json([
                'success' => true,
                'data' => $totals,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate period totals',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category breakdown
     */
    public function categoryBreakdown(Request $request): JsonResponse
    {
        try {
            $companyId = $request->input('company_id');
            $type = $request->input('type', 'expense');
            $fiscalYear = $request->input('fiscal_year');

            $breakdown = $this->transactionService->getCategoryBreakdown(
                $companyId,
                $type,
                $fiscalYear
            );

            return response()->json([
                'success' => true,
                'data' => $breakdown,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get category breakdown',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get monthly trends
     */
    public function monthlyTrends(Request $request): JsonResponse
    {
        try {
            $companyId = $request->input('company_id');
            $fiscalYear = $request->input('fiscal_year');

            $trends = $this->transactionService->getMonthlyTrends($companyId, $fiscalYear);

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
