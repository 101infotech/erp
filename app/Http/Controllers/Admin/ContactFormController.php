<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use App\Models\Site;
use Illuminate\Http\Request;

class ContactFormController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactForm::with('site');

        // Filter by session-selected site
        if (session('selected_site_id')) {
            $query->where('site_id', session('selected_site_id'));
        } elseif ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contactForms = $query->latest()->paginate(20);
        $sites = Site::where('is_active', true)->get();

        return view('admin.contact-forms.index', compact('contactForms', 'sites'));
    }

    public function show(ContactForm $contactForm)
    {
        if ($contactForm->status === 'new') {
            $contactForm->update(['status' => 'read']);
        }

        return view('admin.contact-forms.show', compact('contactForm'));
    }

    public function updateStatus(Request $request, ContactForm $contactForm)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,replied,archived',
        ]);

        $contactForm->update($validated);

        return redirect()->back()
            ->with('success', 'Status updated successfully.');
    }

    public function destroy(ContactForm $contactForm)
    {
        $contactForm->delete();

        return redirect()->route('admin.contact-forms.index')
            ->with('success', 'Contact form deleted successfully.');
    }
}
