<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduleMeeting;
use App\Models\Site;
use Illuminate\Http\Request;

class ScheduleMeetingController extends Controller
{
    public function index(Request $request)
    {
        $query = ScheduleMeeting::with('site');

        // Filter by session-selected site
        if (session('selected_site_id')) {
            $query->where('site_id', session('selected_site_id'));
        } elseif ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $meetings = $query->latest()->paginate(15);
        $sites = Site::where('is_active', true)->get();

        return view('admin.schedule-meetings.index', compact('meetings', 'sites'));
    }

    public function show(ScheduleMeeting $scheduleMeeting)
    {
        return view('admin.schedule-meetings.show', compact('scheduleMeeting'));
    }

    public function updateStatus(Request $request, ScheduleMeeting $scheduleMeeting)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $scheduleMeeting->update($validated);

        return redirect()->route('admin.schedule-meetings.index')
            ->with('success', 'Meeting status updated successfully.');
    }

    public function destroy(ScheduleMeeting $scheduleMeeting)
    {
        $scheduleMeeting->delete();

        return redirect()->route('admin.schedule-meetings.index')
            ->with('success', 'Meeting request deleted successfully.');
    }
}
