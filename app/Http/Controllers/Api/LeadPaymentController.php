<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePaymentRequest;
use App\Http\Requests\Api\UpdatePaymentRequest;
use App\Models\ServiceLead;
use App\Models\LeadPayment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeadPaymentController extends Controller
{
    /**
     * Get all payments for a lead
     */
    public function index(ServiceLead $lead, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $payments = $lead->payments()
                ->with('receivedBy')
                ->orderBy('payment_date', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $payments->items(),
                'pagination' => [
                    'total' => $payments->total(),
                    'per_page' => $payments->perPage(),
                    'current_page' => $payments->currentPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payments',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Record a new payment for a lead
     */
    public function store(ServiceLead $lead, StorePaymentRequest $request): JsonResponse
    {
        try {
            $payment = $lead->payments()->create(array_merge(
                $request->validated(),
                ['received_by_id' => auth()->id()]
            ));

            // Update lead payment tracking
            $totalPaid = $lead->payments()->sum('payment_amount');
            $lead->update([
                'paid_amount' => $totalPaid,
                'payment_received_at' => now(),
                'last_activity_at' => now(),
            ]);

            // Update payment status based on quoted amount
            if ($lead->quoted_amount && $totalPaid >= $lead->quoted_amount) {
                $lead->update(['payment_status' => 'full']);
            } elseif ($totalPaid > 0) {
                $lead->update(['payment_status' => 'partial']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => $payment->load('receivedBy'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display a specific payment
     */
    public function show(ServiceLead $lead, LeadPayment $payment): JsonResponse
    {
        try {
            if ($payment->lead_id !== $lead->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment does not belong to this lead',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $payment->load('receivedBy'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update a payment
     */
    public function update(ServiceLead $lead, LeadPayment $payment, UpdatePaymentRequest $request): JsonResponse
    {
        try {
            if ($payment->lead_id !== $lead->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment does not belong to this lead',
                ], 404);
            }

            $oldAmount = $payment->payment_amount;
            $payment->update($request->validated());

            // Recalculate total payments
            $totalPaid = $lead->payments()->sum('payment_amount');
            $lead->update([
                'paid_amount' => $totalPaid,
                'last_activity_at' => now(),
            ]);

            // Update payment status
            if ($lead->quoted_amount && $totalPaid >= $lead->quoted_amount) {
                $lead->update(['payment_status' => 'full']);
            } elseif ($totalPaid > 0) {
                $lead->update(['payment_status' => 'partial']);
            } else {
                $lead->update(['payment_status' => 'pending']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data' => $payment->load('receivedBy'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete a payment
     */
    public function destroy(ServiceLead $lead, LeadPayment $payment): JsonResponse
    {
        try {
            if ($payment->lead_id !== $lead->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment does not belong to this lead',
                ], 404);
            }

            $payment->delete();

            // Recalculate payment status
            $totalPaid = $lead->payments()->sum('payment_amount');
            $lead->update([
                'paid_amount' => $totalPaid,
                'last_activity_at' => now(),
            ]);

            if ($totalPaid === 0.0 || $totalPaid == 0) {
                $lead->update(['payment_status' => 'pending']);
            } elseif ($lead->quoted_amount && $totalPaid >= $lead->quoted_amount) {
                $lead->update(['payment_status' => 'full']);
            } else {
                $lead->update(['payment_status' => 'partial']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get payment summary for a lead
     */
    public function summary(ServiceLead $lead): JsonResponse
    {
        try {
            $totalPayments = $lead->payments()->sum('payment_amount');
            $advancePayments = $lead->payments()->where('payment_type', 'advance')->sum('payment_amount');
            $partialPayments = $lead->payments()->where('payment_type', 'partial')->sum('payment_amount');
            $fullPayments = $lead->payments()->where('payment_type', 'full')->sum('payment_amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'quoted_amount' => $lead->quoted_amount,
                    'total_paid' => $totalPayments,
                    'pending_amount' => max(0, ($lead->quoted_amount ?? 0) - $totalPayments),
                    'payment_status' => $lead->payment_status,
                    'payment_percentage' => $lead->quoted_amount ? round(($totalPayments / $lead->quoted_amount) * 100, 2) : 0,
                    'by_type' => [
                        'advance' => $advancePayments,
                        'partial' => $partialPayments,
                        'full' => $fullPayments,
                    ],
                    'payment_count' => $lead->payments()->count(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment summary',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
