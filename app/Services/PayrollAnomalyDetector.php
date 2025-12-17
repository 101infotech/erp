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

        // Check for per-day anomalies
        foreach ($attendanceRecords as $attendance) {
            $dayAnomalies = $this->detectDayAnomalies($employee, $attendance);
            $anomalies = $anomalies->merge($dayAnomalies);
        }

        // Check for period-level anomalies (attendance patterns)
        $periodAnomalies = $this->detectPeriodAnomalies($employee, $startDate, $endDate, $attendanceRecords);
        $anomalies = $anomalies->merge($periodAnomalies);

        return $anomalies;
    }

    /**
     * Detect period-level anomalies (attendance patterns)
     *
     * @param HrmEmployee $employee
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param Collection $attendanceRecords
     * @return Collection
     */
    private function detectPeriodAnomalies(HrmEmployee $employee, Carbon $startDate, Carbon $endDate, Collection $attendanceRecords): Collection
    {
        $anomalies = collect();

        // Calculate basic stats
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $weekends = $this->countWeekends($startDate, $endDate);
        $expectedWorkDays = $totalDays - $weekends;
        $daysWorked = $attendanceRecords->where('tracked_hours', '>', 0)->count();
        $absentDays = max(0, $expectedWorkDays - $daysWorked);

        // 1. Check for excessive absences (>40% absent rate)
        if ($expectedWorkDays > 0) {
            $absentRate = ($absentDays / $expectedWorkDays) * 100;
            if ($absentRate > 40) {
                $anomalies->push([
                    'type' => 'excessive_absences',
                    'severity' => 'high',
                    'description' => "Excessive absence rate: {$absentRate}% ({$absentDays} absent out of {$expectedWorkDays} expected work days).",
                    'metadata' => [
                        'absent_days' => $absentDays,
                        'expected_work_days' => $expectedWorkDays,
                        'absent_rate' => round($absentRate, 2),
                    ],
                ]);
            } elseif ($absentRate > 25) {
                $anomalies->push([
                    'type' => 'high_absences',
                    'severity' => 'medium',
                    'description' => "High absence rate: {$absentRate}% ({$absentDays} absent out of {$expectedWorkDays} expected work days).",
                    'metadata' => [
                        'absent_days' => $absentDays,
                        'expected_work_days' => $expectedWorkDays,
                        'absent_rate' => round($absentRate, 2),
                    ],
                ]);
            }
        }

        // 2. Check for low total hours (less than 50% of expected)
        $totalHours = $attendanceRecords->sum('tracked_hours');
        $expectedHours = $expectedWorkDays * 8; // Assuming 8 hours per day
        if ($expectedHours > 0) {
            $hoursRate = ($totalHours / $expectedHours) * 100;
            if ($hoursRate < 50 && $hoursRate > 0) {
                $anomalies->push([
                    'type' => 'low_work_hours',
                    'severity' => 'high',
                    'description' => "Very low work hours: {$totalHours} hours tracked vs {$expectedHours} expected ({$hoursRate}%).",
                    'metadata' => [
                        'total_hours' => round($totalHours, 2),
                        'expected_hours' => $expectedHours,
                        'hours_rate' => round($hoursRate, 2),
                    ],
                ]);
            }
        }

        // 3. Check for consecutive absent days (5+ days in a row without leave request)
        $consecutiveAbsent = $this->findConsecutiveAbsentDays($employee, $startDate, $endDate, $attendanceRecords);
        if ($consecutiveAbsent >= 5) {
            $anomalies->push([
                'type' => 'consecutive_absences',
                'severity' => 'high',
                'description' => "Consecutive absences detected: {$consecutiveAbsent} days in a row without approved leave.",
                'metadata' => [
                    'consecutive_days' => $consecutiveAbsent,
                ],
            ]);
        }

        // 4. Check for pattern of late arrivals (if time entries available)
        $lateArrivals = $this->countLateArrivals($attendanceRecords);
        if ($lateArrivals > 0 && $daysWorked > 0) {
            $lateRate = ($lateArrivals / $daysWorked) * 100;
            if ($lateRate > 50) {
                $anomalies->push([
                    'type' => 'frequent_late_arrivals',
                    'severity' => 'medium',
                    'description' => "Frequent late arrivals: {$lateArrivals} out of {$daysWorked} work days ({$lateRate}%).",
                    'metadata' => [
                        'late_days' => $lateArrivals,
                        'work_days' => $daysWorked,
                        'late_rate' => round($lateRate, 2),
                    ],
                ]);
            }
        }

        return $anomalies;
    }

    /**
     * Count weekends (Saturdays only) in period
     */
    private function countWeekends(Carbon $start, Carbon $end): int
    {
        $weekends = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if ($current->isSaturday()) {
                $weekends++;
            }
            $current->addDay();
        }
        return $weekends;
    }

    /**
     * Find longest consecutive absent days
     */
    private function findConsecutiveAbsentDays(HrmEmployee $employee, Carbon $startDate, Carbon $endDate, Collection $attendanceRecords): int
    {
        $current = $startDate->copy();
        $maxConsecutive = 0;
        $currentConsecutive = 0;

        while ($current->lte($endDate)) {
            // Skip Saturdays (weekends)
            if ($current->isSaturday()) {
                $current->addDay();
                continue;
            }

            $dayRecord = $attendanceRecords->firstWhere('date', $current->format('Y-m-d'));

            if (!$dayRecord || $dayRecord->tracked_hours == 0) {
                $currentConsecutive++;
                $maxConsecutive = max($maxConsecutive, $currentConsecutive);
            } else {
                $currentConsecutive = 0;
            }

            $current->addDay();
        }

        return $maxConsecutive;
    }

    /**
     * Count late arrivals (clock in after 9:30 AM)
     */
    private function countLateArrivals(Collection $attendanceRecords): int
    {
        $lateCount = 0;

        foreach ($attendanceRecords as $record) {
            $entries = $record->timeEntries ?? collect();
            $firstClockIn = $entries->where('type', 'In')->sortBy('time')->first();

            if ($firstClockIn) {
                $clockInTime = Carbon::parse($firstClockIn->time);
                $lateThreshold = $clockInTime->copy()->setTime(9, 30, 0);

                if ($clockInTime->gt($lateThreshold)) {
                    $lateCount++;
                }
            }
        }

        return $lateCount;
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
     * @param HrmAttendanceDay|null $attendance Optional attendance day for day-level anomalies
     * @return void
     */
    public function saveAnomalies(Collection $anomalies, HrmEmployee $employee, ?HrmAttendanceDay $attendance = null): void
    {
        foreach ($anomalies as $anomaly) {
            // For period-level anomalies, check without attendance_day_id
            $query = HrmAttendanceAnomaly::where('employee_id', $employee->id)
                ->where('anomaly_type', $anomaly['type']);

            if ($attendance) {
                $query->where('attendance_day_id', $attendance->id);
            } else {
                $query->whereNull('attendance_day_id');
            }

            $existing = $query->first();

            if (!$existing) {
                $dateValue = $attendance?->date;
                if ($dateValue && $dateValue instanceof \Carbon\Carbon) {
                    $dateValue = $dateValue->format('Y-m-d');
                } elseif (!$dateValue) {
                    $dateValue = now()->format('Y-m-d');
                }

                HrmAttendanceAnomaly::create([
                    'employee_id' => $employee->id,
                    'attendance_day_id' => $attendance?->id,
                    'date' => $dateValue,
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
     * Save period-level anomalies
     *
     * @param Collection $anomalies
     * @param HrmEmployee $employee
     * @param Carbon $periodDate
     * @return void
     */
    public function savePeriodAnomalies(Collection $anomalies, HrmEmployee $employee, Carbon $periodDate): void
    {
        foreach ($anomalies as $anomaly) {
            // Check if period anomaly already exists for this type and period
            $existing = HrmAttendanceAnomaly::where('employee_id', $employee->id)
                ->where('anomaly_type', $anomaly['type'])
                ->whereNull('attendance_day_id')
                ->whereDate('date', $periodDate->format('Y-m-d'))
                ->first();

            if (!$existing) {
                HrmAttendanceAnomaly::create([
                    'employee_id' => $employee->id,
                    'attendance_day_id' => null, // Period-level anomaly
                    'date' => $periodDate->format('Y-m-d'),
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
