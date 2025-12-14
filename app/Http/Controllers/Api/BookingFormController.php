<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingForm;
use App\Models\Site;
use Illuminate\Http\Request;

class BookingFormController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
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
            'marketing_goals' => 'nullable|string',
            'current_challenges' => 'nullable|string',
        ]);

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
            'marketing_goals' => $validated['marketing_goals'] ?? null,
            'current_challenges' => $validated['current_challenges'] ?? null,
            'ip_address' => $request->ip(),
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking form submitted successfully.',
            'data' => $bookingForm,
        ], 201);
    }
}
