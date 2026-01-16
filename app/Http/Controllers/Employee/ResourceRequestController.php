<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\HrmEmployee;
use App\Models\HrmResourceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ResourceRequestController extends Controller
{
    public function index(Request $request)
    {
        $employee = $this->currentEmployee();
        if (!$employee) {
            return view('employee.resource-requests.index', [
                'requests' => collect(),
                'employeeMissing' => true,
            ]);
        }

        $query = HrmResourceRequest::where('employee_id', $employee->id)->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $requests = $query->paginate(15);

        return view('employee.resource-requests.index', compact('requests'));
    }

    public function create()
    {
        $employee = $this->currentEmployee();
        if (!$employee) {
            return redirect()->route('employee.resource-requests.index')
                ->with('error', 'No employee profile linked to your account.');
        }

        return view('employee.resource-requests.create');
    }

    public function store(Request $request)
    {
        $employee = $this->currentEmployee();
        if (!$employee) {
            return redirect()->route('employee.resource-requests.index')
                ->with('error', 'No employee profile linked to your account.');
        }

        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'priority' => 'required|in:low,medium,high,urgent',
            'category' => 'required|in:office_supplies,equipment,pantry,furniture,technology,other',
            'reason' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            HrmResourceRequest::create(array_merge($validated, [
                'employee_id' => $employee->id,
            ]));

            DB::commit();

            return redirect()
                ->route('employee.resource-requests.index')
                ->with('success', 'Request submitted.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee resource request failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to submit request. Please try again.');
        }
    }

    public function show(HrmResourceRequest $resourceRequest)
    {
        $employee = $this->currentEmployee();
        if (!$employee) {
            return redirect()->route('employee.resource-requests.index')
                ->with('error', 'No employee profile linked to your account.');
        }

        // Check if the request belongs to the current employee
        if ($resourceRequest->employee_id !== $employee->id) {
            return redirect()->route('employee.resource-requests.index')
                ->with('error', 'Unauthorized access.');
        }

        return view('employee.resource-requests.show', compact('resourceRequest'));
    }

    private function currentEmployee(): ?HrmEmployee
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return HrmEmployee::where('user_id', $user->id)->first();
    }
}
