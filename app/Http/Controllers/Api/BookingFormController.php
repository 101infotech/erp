<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingForm;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingFormController extends Controller
{
    /**
     * Store a new booking form submission (Public)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_slug' => 'required|exists:sites,slug',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:255',
            'services_interested' => 'nullable|array',
            'investment_range' => 'nullable|string|max:255',
            'flight_timeline' => 'nullable|string|max:255',
            'current_marketing_efforts' => 'nullable|string',
            'marketing_goals' => 'nullable|string',
            'current_challenges' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $site = Site::where('slug', $validated['site_slug'])->firstOrFail();

        $bookingForm = BookingForm::create([
            'site_id' => $site->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'company_name' => $validated['company_name'] ?? null,
            'website_url' => $validated['website_url'] ?? null,
            'industry' => $validated['industry'] ?? null,
            'services_interested' => $validated['services_interested'] ?? null,
            'investment_range' => $validated['investment_range'] ?? null,
            'flight_timeline' => $validated['flight_timeline'] ?? null,
            'current_marketing_efforts' => $validated['current_marketing_efforts'] ?? null,
            'marketing_goals' => $validated['marketing_goals'] ?? null,
            'current_challenges' => $validated['current_challenges'] ?? null,
            'ip_address' => $request->ip(),
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your interest! Our team will review your information and reach out within 24 hours to schedule your complimentary Brand Flight Consultation.',
            'data' => [
                'id' => $bookingForm->id,
                'first_name' => $bookingForm->first_name,
                'last_name' => $bookingForm->last_name,
                'email' => $bookingForm->email,
                'company_name' => $bookingForm->company_name,
                'status' => $bookingForm->status,
                'created_at' => $bookingForm->created_at->toISOString(),
            ],
        ], 201);
    }

    /**
     * Get all booking form submissions (Admin)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = BookingForm::with('site')->latest();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by site
        if ($request->has('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $bookings = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ], 200);
    }

    /**
     * Get a single booking form submission (Admin)
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $booking = BookingForm::with('site')->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $booking,
        ], 200);
    }

    /**
     * Update booking status (Admin)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = BookingForm::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:new,contacted,scheduled,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $booking->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully',
            'data' => $booking,
        ], 200);
    }

    /**
     * Delete a booking form submission (Admin)
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $booking = BookingForm::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found',
            ], 404);
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully',
        ], 200);
    }
}
