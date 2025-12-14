<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required|string|exists:sites,slug',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string',
            'source' => 'nullable|string|max:100',
            'meta' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $site = Site::where('slug', $request->brand)->first();

        try {
            $lead = Lead::create([
                'site_id' => $site->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'source' => $request->source ?? 'api',
                'meta' => $request->meta,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'new',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Lead submitted successfully',
                'data' => [
                    'id' => $lead->id,
                    'reference' => 'LEAD-' . str_pad($lead->id, 6, '0', STR_PAD_LEFT),
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit lead'
            ], 500);
        }
    }
}
