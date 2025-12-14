<?php

namespace App\Services;

use App\Models\HrmEmployee;
use App\Models\HrmAttendanceDay;
use App\Models\HrmAttendanceAnomaly;
use App\Models\HrmTimeEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Payroll Anomaly Detector Service
 * 
 * Detects and flags suspicious attendance patterns that may affect payroll accuracy
 */
class PayrollAnomalyDetector
{
    /**
     * Detect anomalies for a specific employee in a date range
     *
     * @param HrmEmployee $employee
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection Collection of detected anomalies
     */
    public function detectAnomaliesForEmployee(HrmEmployee $employee, Carbon $startDate, Carbon $endDate): Collection
    {
        $anomalies = collect();

        // Get attendance records for the period
        $attendanceRecords = HrmAttendanceDay::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('timeEntries')
            ->get();

        foreach ($attendanceRecords as $attendance) {
            $dayAnomalies = $this->detectDayAnomalies($employee, $attendance);
            $anomalies = $anomalies->merge($dayAnomalies);
        }

        return $anomalies;
    }

    /**
     * Detect anomalies for a single attendance day
     *
     * @param HrmEmployee $employee
     * @param HrmAttendanceDay $attendance
     * @return Collection
     */
    public function detectDayAnomalies(HrmEmployee $employee, HrmAttendanceDay $attendance): Collection
    {
        $anomalies = collect();

        // Check for missing clock out
        $missingClockOut = $this->checkMissingClockOut($attendance);
        if ($missingClockOut) {
            $anomalies->push($missingClockOut);
        }

        // Check for excessive hours (>16 hours per day)
        $excessiveHours = $this->checkExcessiveHours($attendance);
        if ($excessiveHours) {
            $anomalies->push($excessiveHours);
        }

        // Check for weekend work without OT request
        $weekendWork = $this->checkWeekendWorkWithoutOT($attendance);
        if ($weekendWork) {
            $anomalies->push($weekendWork);
        }

        // Check for location inconsistencies
        $locationAnomaly = $this->checkLocationInconsistency($attendance);
        if ($locationAnomaly) {
            $anomalies->push($locationAnomaly);
        }

        // Check for duplicate entries
        $duplicateEntry = $this->checkDuplicateEntries($attendance);
        if ($duplicateEntry) {
            $anomalies->push($duplicateEntry);
        }

        // Check for negative time (clock out before clock in)
        $negativeTime = $this->checkNegativeTime($attendance);
        if ($negativeTime) {
            $anomalies->push($negativeTime);
        }

        return $anomalies;
    }

    /**
     * Check for missing clock-out entries
     */
    private function checkMissingClockOut(HrmAttendanceDay $attendance): ?array
    {
        $entries = $attendance->timeEntries ?? collect();

        $clockIns = $entries->where('type', 'In')->count();
        $clockOuts = $entries->where('type', 'Out')->count();

        if ($clockIns > $clockOuts) {
            return [
                'type' => 'missing_clock_out',
                'severity' => 'high',
                'description' => "Missing clock-out entry. {$clockIns} clock-ins but only {$clockOuts} clock-outs.",
                'metadata' => [
                    'clock_ins' => $clockIns,
                    'clock_outs' => $clockOuts,
                ],
            ];
        }

        return null;
    }

    /**
     * Check for excessive hours (>16 hours)
     */
    private function checkExcessiveHours(HrmAttendanceDay $attendance): ?array
    {
        if ($attendance->tracked_hours > 16) {
            return [
                'type' => 'excessive_hours',
                'severity' => 'high',
                'description' => "Excessive hours worked: {$attendance->tracked_hours} hours in one day (>16 hours threshold).",
                'metadata' => [
                    'tracked_hours' => $attendance->tracked_hours,
                    'threshold' => 16,
                ],
            ];
        }

        return null;
    }

    /**
     * Check for weekend work without OT
     */
    private function checkWeekendWorkWithoutOT(HrmAttendanceDay $attendance): ?array
    {
        $date = Carbon::parse($attendance->date);

        // Only check Saturday (Sunday is working day per requirements)
        if ($date->isSaturday() && $attendance->tracked_hours > 0 && $attendance->overtime_hours == 0) {
            return [
                'type' => 'weekend_work_no_ot',
                'severity' => 'medium',
                'description' => "Saturday work detected ({$attendance->tracked_hours} hours) but no overtime recorded.",
                'metadata' => [
                    'tracked_hours' => $attendance->tracked_hours,
                    'overtime_hours' => $attendance->overtime_hours,
                    'day' => $date->format('l'),
                ],
            ];
        }

        return null;
    }

    /**
     * Check for location inconsistencies
     */
    private function checkLocationInconsistency(HrmAttendanceDay $attendance): ?array
    {
        $entries = $attendance->timeEntries ?? collect();

        if ($entries->count() < 2) {
            return null;
        }

        // Get all unique locations
        $locations = $entries->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->map(function ($entry) {
                return [
                    'lat' => $entry->latitude,
                    'lng' => $entry->longitude,
                ];
            })
            ->unique()
            ->values();

        // If more than 3 different locations in a day, flag as inconsistent
        if ($locations->count() > 3) {
            return [
                'type' => 'location_inconsistency',
                'severity' => 'low',
                'description' => "Multiple locations detected ({$locations->count()} different locations). May indicate location spoofing or field work.",
                'metadata' => [
                    'location_count' => $locations->count(),
                    'locations' => $locations->toArray(),
                ],
            ];
        }

        return null;
    }

    /**
     * Check for duplicate time entries
     */
    private function checkDuplicateEntries(HrmAttendanceDay $attendance): ?array
    {
        $entries = $attendance->timeEntries ?? collect();

        if ($entries->count() < 2) {
            return null;
        }

        // Group by time and check for duplicates within 1 minute
        $duplicates = $entries->groupBy(function ($entry) {
            return Carbon::parse($entry->time)->format('Y-m-d H:i');
        })->filter(function ($group) {
            return $group->count() > 1;
        });

        if ($duplicates->count() > 0) {
            return [
                'type' => 'duplicate_entry',
                'severity' => 'medium',
                'description' => "Duplicate time entries detected at {$duplicates->count()} different times.",
                'metadata' => [
                    'duplicate_count' => $duplicates->count(),
                    'times' => $duplicates->keys()->toArray(),
                ],
            ];
        }

        return null;
    }

    /**
     * Check for negative time (clock out before clock in)
     */
    private function checkNegativeTime(HrmAttendanceDay $attendance): ?array
    {
        $entries = $attendance->timeEntries ?? collect();

        if ($entries->count() < 2) {
            return null;
        }

        // Get pairs of clock in/out
        $sorted = $entries->sortBy('time');
        $previous = null;

        foreach ($sorted as $entry) {
            if ($previous && $previous->type === 'Out' && $entry->type === 'In') {
                $prevTime = Carbon::parse($previous->time);
                $currentTime = Carbon::parse($entry->time);

                if ($currentTime->lt($prevTime)) {
                    return [
                        'type' => 'negative_time',
                        'severity' => 'high',
                        'description' => "Clock-in time is before previous clock-out time. Possible data error.",
                        'metadata' => [
                            'clock_out' => $prevTime->toDateTimeString(),
                            'clock_in' => $currentTime->toDateTimeString(),
                        ],
                    ];
                }
            }

            $previous = $entry;
        }

        return null;
    }

    /**
     * Save detected anomalies to database
     *
     * @param Collection $anomalies Array of anomaly data
     * @param HrmEmployee $employee
     * @param HrmAttendanceDay $attendance
     * @return void
     */
    public function saveAnomalies(Collection $anomalies, HrmEmployee $employee, HrmAttendanceDay $attendance): void
    {
        foreach ($anomalies as $anomaly) {
            // Check if this anomaly already exists
            $existing = HrmAttendanceAnomaly::where('employee_id', $employee->id)
                ->where('attendance_day_id', $attendance->id)
                ->where('anomaly_type', $anomaly['type'])
                ->first();

            if (!$existing) {
                HrmAttendanceAnomaly::create([
                    'employee_id' => $employee->id,
                    'attendance_day_id' => $attendance->id,
                    'date' => $attendance->date,
                    'anomaly_type' => $anomaly['type'],
                    'description' => $anomaly['description'],
                    'metadata' => $anomaly['metadata'] ?? null,
                    'severity' => $anomaly['severity'],
                    'reviewed' => false,
                ]);
            }
        }
    }

    /**
     * Get summary of anomalies for payroll review
     *
     * @param int $employeeId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getAnomalySummary(int $employeeId, Carbon $startDate, Carbon $endDate): array
    {
        $anomalies = HrmAttendanceAnomaly::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        return [
            'total_count' => $anomalies->count(),
            'unreviewed_count' => $anomalies->where('reviewed', false)->count(),
            'by_severity' => [
                'high' => $anomalies->where('severity', 'high')->count(),
                'medium' => $anomalies->where('severity', 'medium')->count(),
                'low' => $anomalies->where('severity', 'low')->count(),
            ],
            'by_type' => $anomalies->groupBy('anomaly_type')->map->count()->toArray(),
            'requires_review' => $anomalies->where('reviewed', false)->where('severity', 'high')->count() > 0,
        ];
    }
}
