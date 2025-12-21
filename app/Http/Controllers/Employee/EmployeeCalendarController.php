<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class EmployeeCalendarController extends Controller
{
    public function index(Request $request)
    {
        // Load active holidays
        $holidays = Holiday::where('is_active', true)
            ->orderBy('date', 'asc')
            ->get();

        // For now we only merge holidays; attendance/events can be added here later
        return view('employee.calendar.index', compact('holidays'));
    }
}
