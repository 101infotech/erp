<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingForm;
use App\Models\Site;
use Illuminate\Http\Request;

class BookingFormController extends Controller
{
    public function index(Request $request)
    {
        $query = BookingForm::with('site');

        // Filter by session-selected site
        if (session('selected_site_id')) {
            $query->where('site_id', session('selected_site_id'));
        } elseif ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookingForms = $query->latest()->paginate(20);
        $sites = Site::where('is_active', true)->get();

        return view('admin.booking-forms.index', compact('bookingForms', 'sites'));
    }

    public function show(BookingForm $bookingForm)
    {
        if ($bookingForm->status === 'new') {
            $bookingForm->update(['status' => 'contacted']);
        }

        return view('admin.booking-forms.show', compact('bookingForm'));
    }

    public function updateStatus(Request $request, BookingForm $bookingForm)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,scheduled,completed,cancelled',
        ]);

        $bookingForm->update($validated);

        return redirect()->back()
            ->with('success', 'Status updated successfully.');
    }

    public function destroy(BookingForm $bookingForm)
    {
        $bookingForm->delete();

        return redirect()->route('admin.booking-forms.index')
            ->with('success', 'Booking form deleted successfully.');
    }
}
