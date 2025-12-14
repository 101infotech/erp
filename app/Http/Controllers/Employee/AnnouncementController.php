<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements for the employee
     */
    public function index()
    {
        $announcements = Announcement::published()
            ->forUser(Auth::id())
            ->with('creator')
            ->latest()
            ->paginate(15);

        return view('employee.announcements.index', compact('announcements'));
    }

    /**
     * Display the specified announcement
     */
    public function show(Announcement $announcement)
    {
        // Check if user has access to this announcement
        $hasAccess = $announcement->recipient_type === 'all' ||
            ($announcement->recipient_type === 'specific' &&
                in_array(Auth::id(), $announcement->recipient_ids ?? []));

        if (!$hasAccess || !$announcement->is_published) {
            abort(403, 'You do not have access to this announcement.');
        }

        $announcement->load('creator');
        return view('employee.announcements.show', compact('announcement'));
    }
}
