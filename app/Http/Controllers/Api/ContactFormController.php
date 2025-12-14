<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use App\Models\Site;
use Illuminate\Http\Request;

class ContactFormController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_slug' => 'required|exists:sites,slug',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $site = Site::where('slug', $validated['site_slug'])->firstOrFail();

        $contactForm = ContactForm::create([
            'site_id' => $site->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contact form submitted successfully.',
            'data' => $contactForm,
        ], 201);
    }
}
