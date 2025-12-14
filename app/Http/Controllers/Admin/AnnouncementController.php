<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Mail\AnnouncementMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::with('creator')
            ->latest()
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        return view('admin.announcements.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,normal,high',
            'recipient_type' => 'required|in:all,specific',
            'recipient_ids' => 'nullable|array',
            'recipient_ids.*' => 'exists:users,id',
            'send_email' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['send_email'] = $request->has('send_email');
        $validated['is_published'] = $request->has('is_published');

        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        $announcement = Announcement::create($validated);

        // Send emails if requested
        if ($announcement->send_email && $announcement->is_published) {
            $this->sendAnnouncementEmails($announcement);
        }

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        $announcement->load('creator');
        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        return view('admin.announcements.edit', compact('announcement', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,normal,high',
            'recipient_type' => 'required|in:all,specific',
            'recipient_ids' => 'nullable|array',
            'recipient_ids.*' => 'exists:users,id',
            'send_email' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $validated['send_email'] = $request->has('send_email');
        $wasPublished = $announcement->is_published;
        $validated['is_published'] = $request->has('is_published');

        if ($validated['is_published'] && !$wasPublished) {
            $validated['published_at'] = now();
        }

        $announcement->update($validated);

        // Send emails if newly published and email is enabled
        if ($announcement->send_email && $announcement->is_published && !$wasPublished) {
            $this->sendAnnouncementEmails($announcement);
        }

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }

    /**
     * Send announcement emails to recipients
     */
    private function sendAnnouncementEmails(Announcement $announcement)
    {
        try {
            if ($announcement->recipient_type === 'all') {
                $recipients = User::where('role', 'user')->get();
            } else {
                $recipients = User::whereIn('id', $announcement->recipient_ids ?? [])->get();
            }

            foreach ($recipients as $recipient) {
                Mail::to($recipient->email)->send(new AnnouncementMail($announcement, $recipient));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send announcement emails: ' . $e->getMessage());
        }
    }
}
