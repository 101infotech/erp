<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Holiday;

class EmployeeHolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::where('is_active', true)
            ->orderBy('date', 'asc')
            ->get();

        return view('employee.holidays.index', compact('holidays'));
    }
}
