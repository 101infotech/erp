<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HrmEmployee;
use Illuminate\Http\Request;

class HrmEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = HrmEmployee::with(['company', 'department']);

        // Filter by company slug or ID
        if ($request->filled('company')) {
            $query->whereHas('company', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->company}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }

        $employees = $query->orderBy('full_name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $employees
        ]);
    }

    public function show($id)
    {
        $employee = HrmEmployee::with(['company', 'department', 'attendanceDays'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $employee
        ]);
    }
}
