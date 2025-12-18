<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HrmHolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'desc')->paginate(20);
        return view('admin.hrm.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'is_company_wide' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_company_wide'] = $request->boolean('is_company_wide', true);
        $data['is_active'] = $request->boolean('is_active', true);

        Holiday::create($data);

        return redirect()->route('admin.hrm.holidays.index')->with('success', 'Holiday added successfully.');
    }

    public function edit(Holiday $holiday)
    {
        $holidays = Holiday::orderBy('date', 'desc')->paginate(20);
        return view('admin.hrm.holidays.index', compact('holiday', 'holidays'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'is_company_wide' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_company_wide'] = $request->boolean('is_company_wide', true);
        $data['is_active'] = $request->boolean('is_active', true);

        $holiday->update($data);

        return redirect()->route('admin.hrm.holidays.index')->with('success', 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('admin.hrm.holidays.index')->with('success', 'Holiday deleted successfully.');
    }
}
