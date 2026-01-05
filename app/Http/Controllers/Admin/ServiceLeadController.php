<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceLead;
use App\Models\LeadStatus;
use App\Models\User;
use App\Mail\LeadAssignedMail;
use App\Mail\LeadStatusChangedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ServiceLeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ServiceLead::with(['assignedTo', 'createdBy'])
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc');

        // If employee, show only their assigned leads
        if ($request->user()->role === 'employee') {
            $query->where('inspection_assigned_to', $request->user()->id);
        }

        // Search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Assigned to filter (admin only)
        if ($request->filled('assigned_to') && $request->user()->role === 'admin') {
            $query->assignedTo($request->assigned_to);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->paginate(20);
        $statuses = LeadStatus::getAllActive();
        $users = User::orderBy('name')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $leads,
                'statuses' => $statuses,
            ]);
        }

        return view('admin.leads.index', compact('leads', 'statuses', 'users'));
    }

    /**
     * Get all active statuses
     */
    public function statuses()
    {
        $statuses = LeadStatus::getAllActive();
        return response()->json([
            'success' => true,
            'data' => $statuses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = LeadStatus::getAllActive();
        $users = User::orderBy('name')->get();

        return view('admin.leads.create', compact('statuses', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:255',
            'service_requested' => 'required|string|max:255',
            'location' => 'required|string|max:500',
            'phone_number' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'inspection_date' => 'nullable|date',
            'inspection_time' => 'nullable|date_format:H:i',
            'inspection_charge' => 'nullable|numeric|min:0',
            'inspection_report_date' => 'nullable|date',
            // UI uses assigned_to; DB column is inspection_assigned_to
            'assigned_to' => 'nullable|exists:users,id',
            'inspection_assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();
            if (array_key_exists('assigned_to', $data)) {
                $data['inspection_assigned_to'] = $data['assigned_to'];
                unset($data['assigned_to']);
            }

            $lead = ServiceLead::create($data);

            // Send notification email if assigned
            if ($lead->inspection_assigned_to) {
                $assignedUser = User::find($lead->inspection_assigned_to);
                if ($assignedUser) {
                    Mail::to($assignedUser->email)
                        ->queue(new LeadAssignedMail($lead, $assignedUser));
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead created successfully',
                    'data' => $lead->load(['assignedTo', 'createdBy']),
                ], 201);
            }

            return redirect()->route('admin.leads.index')
                ->with('success', 'Lead created successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create lead',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create lead')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceLead $lead)
    {
        $lead->load(['assignedTo', 'createdBy', 'statusInfo']);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $lead,
            ]);
        }

        return view('admin.leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceLead $lead)
    {
        $statuses = LeadStatus::getAllActive();
        $users = User::orderBy('name')->get();

        return view('admin.leads.edit', compact('lead', 'statuses', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceLead $lead)
    {
        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:255',
            'service_requested' => 'required|string|max:255',
            'location' => 'required|string|max:500',
            'phone_number' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'inspection_date' => 'nullable|date',
            'inspection_time' => 'nullable|date_format:H:i',
            'inspection_charge' => 'nullable|numeric|min:0',
            'inspection_report_date' => 'nullable|date',
            // UI uses assigned_to; DB column is inspection_assigned_to
            'assigned_to' => 'nullable|exists:users,id',
            'inspection_assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $oldAssignee = $lead->inspection_assigned_to;
            $data = $validator->validated();
            if (array_key_exists('assigned_to', $data)) {
                $data['inspection_assigned_to'] = $data['assigned_to'];
                unset($data['assigned_to']);
            }

            $lead->update($data);

            // Send notification if assignment changed
            if ($oldAssignee != $lead->inspection_assigned_to && $lead->inspection_assigned_to) {
                $assignedUser = User::find($lead->inspection_assigned_to);
                if ($assignedUser) {
                    Mail::to($assignedUser->email)
                        ->queue(new LeadAssignedMail($lead, $assignedUser));
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead updated successfully',
                    'data' => $lead->load(['assignedTo', 'createdBy']),
                ]);
            }

            return redirect()->route('admin.leads.index')
                ->with('success', 'Lead updated successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update lead',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update lead')
                ->withInput();
        }
    }

    /**
     * Update lead status
     */
    public function updateStatus(Request $request, ServiceLead $lead)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|exists:lead_statuses,status_key',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $oldStatus = $lead->status;
            $lead->update(['status' => $request->status]);

            // Send email notification for important status changes
            if ($lead->assignedTo && in_array($request->status, ['Inspection Booked', 'Positive', 'Reports Sent', 'Cancelled'])) {
                Mail::to($lead->assignedTo->email)
                    ->queue(new LeadStatusChangedMail(
                        $lead,
                        $oldStatus,
                        $request->status,
                        $request->user()
                    ));
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully',
                    'data' => $lead->load(['assignedTo', 'createdBy']),
                ]);
            }

            return redirect()->back()
                ->with('success', 'Status updated successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update status');
        }
    }

    /**
     * Assign lead to user
     */
    public function assign(Request $request, ServiceLead $lead)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $lead->update(['inspection_assigned_to' => $request->assigned_to]);

            // Send notification email to assigned user
            if ($request->assigned_to) {
                $assignedUser = User::find($request->assigned_to);
                if ($assignedUser) {
                    Mail::to($assignedUser->email)
                        ->queue(new LeadAssignedMail($lead, $assignedUser));
                }
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead assigned successfully',
                    'data' => $lead->load(['assignedTo', 'createdBy']),
                ]);
            }

            return redirect()->back()
                ->with('success', 'Lead assigned successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to assign lead',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to assign lead');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceLead $lead)
    {
        try {
            $lead->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead deleted successfully',
                ]);
            }

            return redirect()->route('admin.leads.index')
                ->with('success', 'Lead deleted successfully');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete lead',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to delete lead');
        }
    }
}
