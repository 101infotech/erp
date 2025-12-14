<?php

namespace App\Services;

use App\Models\HrmAttendanceDay;
use App\Models\HrmEmployee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class JibbleTimesheetService
{
    public function __construct(private readonly JibbleAuthService $authService) {}

    /**
     * Parse ISO 8601 duration (e.g., "P1D", "PT8H30M", "PT0S", "PT8H30M45.123S") to decimal hours
     */
    private function parseDuration(string $duration): float
    {
        if ($duration === 'PT0S' || empty($duration)) {
            return 0.0;
        }

        try {
            $hours = 0.0;

            // Handle fractional seconds by extracting and processing separately
            // Match pattern: PT8H30M45.123456S or PT8H30M or P1D
            if (preg_match('/^P(?:(\d+)D)?(?:T(?:(\d+)H)?(?:(\d+)M)?(?:(\d+(?:\.\d+)?)S)?)?$/', $duration, $matches)) {
                $days = isset($matches[1]) && $matches[1] !== '' ? (int)$matches[1] : 0;
                $hoursMatch = isset($matches[2]) && $matches[2] !== '' ? (int)$matches[2] : 0;
                $minutes = isset($matches[3]) && $matches[3] !== '' ? (int)$matches[3] : 0;
                $seconds = isset($matches[4]) && $matches[4] !== '' ? (float)$matches[4] : 0.0;

                // Convert to hours
                $hours += $days * 24;
                $hours += $hoursMatch;
                $hours += $minutes / 60;
                $hours += $seconds / 3600;

                return round($hours, 2);
            }

            Log::warning('Failed to parse duration - invalid format', ['duration' => $duration]);
            return 0.0;
        } catch (\Exception $e) {
            Log::warning('Failed to parse duration', ['duration' => $duration, 'error' => $e->getMessage()]);
            return 0.0;
        }
    }

    /**
     * Sync timesheets from Jibble using TimesheetsSummary endpoint
     */
    public function syncDailySummary(Carbon|string $startDate, Carbon|string $endDate, array $personIds = []): int
    {
        $token = $this->authService->getToken();
        $summaryUrl = 'https://time-attendance.prod.jibble.io/v1/TimesheetsSummary';

        $start = Carbon::parse($startDate)->toDateString();
        $end = Carbon::parse($endDate)->toDateString();

        $query = [
            'period' => 'Custom',
            'date' => $start,
            'endDate' => $end,
        ];

        // Add person IDs if specified
        if (!empty($personIds)) {
            $query['personIds'] = $personIds;
        }

        try {
            $response = Http::timeout(120)
                ->withToken($token)
                ->get($summaryUrl, $query);

            if ($response->failed()) {
                Log::error('Jibble TimesheetsSummary API failed', [
                    'url' => $summaryUrl,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new RuntimeException('Failed to fetch timesheets from Jibble API. Status: ' . $response->status());
            }

            $data = $response->json();
            $synced = 0;

            if (isset($data['value']) && is_array($data['value'])) {
                foreach ($data['value'] as $personData) {
                    $synced += $this->processPersonSummary($personData);
                }
            }

            return $synced;
        } catch (\Exception $e) {
            Log::error('Jibble sync failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Process individual person summary data from TimesheetsSummary response
     */
    private function processPersonSummary(array $personData): int
    {
        $jibblePersonId = $personData['personId'] ?? null;
        if (!$jibblePersonId) {
            return 0;
        }

        $employee = HrmEmployee::where('jibble_person_id', $jibblePersonId)->first();
        if (!$employee) {
            Log::warning('Employee not found for Jibble person', ['jibble_person_id' => $jibblePersonId]);
            return 0;
        }

        $synced = 0;
        $dailyData = $personData['daily'] ?? [];

        foreach ($dailyData as $day) {
            try {
                $date = $day['date'] ?? null;
                if (!$date) {
                    continue;
                }

                // Ensure date is in YYYY-MM-DD format (no time component)
                $dateObject = Carbon::parse($date)->startOfDay();
                $dateString = $dateObject->toDateString();
                $trackedHours = $this->parseDuration($day['tracked'] ?? 'PT0S');
                $regularHours = $this->parseDuration($day['regular'] ?? 'PT0S');
                $overtimeHours = $this->parseDuration($day['dailyOvertime'] ?? 'PT0S');

                // Skip days with no tracked time
                if ($trackedHours <= 0) {
                    continue;
                }

                // Use DB transaction to avoid race conditions
                DB::transaction(function () use ($employee, $dateString, $trackedHours, $regularHours, $overtimeHours, $day) {
                    $existing = HrmAttendanceDay::where('employee_id', $employee->id)
                        ->where('date', $dateString)
                        ->lockForUpdate()
                        ->first();

                    if ($existing) {
                        $existing->update([
                            'tracked_hours' => round($trackedHours, 2),
                            'payroll_hours' => round($regularHours, 2),
                            'overtime_hours' => round($overtimeHours, 2),
                            'source' => 'jibble',
                            'jibble_data' => $day,
                            'notes' => null,
                        ]);
                    } else {
                        HrmAttendanceDay::create([
                            'employee_id' => $employee->id,
                            'date' => $dateString,
                            'tracked_hours' => round($trackedHours, 2),
                            'payroll_hours' => round($regularHours, 2),
                            'overtime_hours' => round($overtimeHours, 2),
                            'source' => 'jibble',
                            'jibble_data' => $day,
                            'notes' => null,
                        ]);
                    }
                });

                $synced++;
            } catch (\Exception $e) {
                Log::error('Failed to process attendance day', [
                    'employee_id' => $employee->id,
                    'date' => $date ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        return $synced;
    }

    /**
     * Fetch detailed timesheets for specific employees using Timesheets endpoint
     */
    public function fetchDetailedTimesheets(Carbon|string $date, string $period = 'Day', array $personIds = []): array
    {
        $token = $this->authService->getToken();
        $timesheetsUrl = 'https://time-attendance.prod.jibble.io/v1/Timesheets';

        $query = [
            'date' => Carbon::parse($date)->toDateString(),
            'period' => $period,
            '$count' => 'true',
            '$expand' => 'person',
            '$orderby' => 'person/fullName asc',
            '$skip' => 0,
            '$top' => 100,
        ];

        $allTimesheets = [];
        $skip = 0;

        do {
            $query['$skip'] = $skip;

            $response = Http::withToken($token)->get($timesheetsUrl, $query);

            if ($response->failed()) {
                Log::error('Jibble Timesheets API failed', [
                    'url' => $timesheetsUrl,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new RuntimeException('Failed to fetch detailed timesheets from Jibble');
            }

            $data = $response->json();
            $timesheets = $data['value'] ?? [];

            foreach ($timesheets as $timesheet) {
                // Filter by person IDs if specified
                if (!empty($personIds) && !in_array($timesheet['personId'] ?? null, $personIds)) {
                    continue;
                }

                $allTimesheets[] = $timesheet;
            }

            $count = $data['@odata.count'] ?? 0;
            $skip += count($timesheets);

            // Continue if there are more records
        } while ($skip < $count && count($timesheets) > 0);

        return $allTimesheets;
    }

    /**
     * Sync and return employee attendance data
     */
    public function syncAndFetchEmployeeAttendance(int $employeeId, Carbon|string $startDate, Carbon|string $endDate): array
    {
        $employee = HrmEmployee::find($employeeId);

        if (!$employee || !$employee->jibble_person_id) {
            throw new RuntimeException('Employee not found or missing Jibble person ID');
        }

        // Sync from Jibble
        $this->syncDailySummary($startDate, $endDate, [$employee->jibble_person_id]);

        // Return stored attendance records
        return HrmAttendanceDay::where('employee_id', $employeeId)
            ->whereBetween('date', [
                Carbon::parse($startDate)->toDateString(),
                Carbon::parse($endDate)->toDateString()
            ])
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Fetch employee timesheets with detailed breakdown
     */
    public function fetchEmployeeTimesheets(string $jibblePersonId, Carbon|string $startDate, Carbon|string $endDate): array
    {
        $token = $this->authService->getToken();
        $summaryUrl = 'https://time-attendance.prod.jibble.io/v1/TimesheetsSummary';

        $query = [
            'period' => 'Custom',
            'date' => Carbon::parse($startDate)->toDateString(),
            'endDate' => Carbon::parse($endDate)->toDateString(),
            'personIds' => [$jibblePersonId],
        ];

        $response = Http::withToken($token)->get($summaryUrl, $query);

        if ($response->failed()) {
            throw new RuntimeException('Failed to fetch employee timesheets from Jibble');
        }

        $data = $response->json();
        return $data['value'] ?? [];
    }

    /**
     * Sync detailed timesheets and store comprehensive data
     */
    public function syncDetailedTimesheets(Carbon|string $date, string $period = 'Day'): int
    {
        $timesheets = $this->fetchDetailedTimesheets($date, $period);
        $synced = 0;

        foreach ($timesheets as $timesheet) {
            $jibblePersonId = $timesheet['personId'] ?? null;
            if (!$jibblePersonId) {
                continue;
            }

            $employee = HrmEmployee::where('jibble_person_id', $jibblePersonId)->first();
            if (!$employee) {
                continue;
            }

            $daily = $timesheet['daily'] ?? [];
            foreach ($daily as $day) {
                try {
                    $date = $day['date'] ?? null;
                    if (!$date) {
                        continue;
                    }

                    $trackedTotal = $this->parseDuration($day['trackedHours']['total'] ?? 'PT0S');
                    $payrollTotal = $this->parseDuration($day['payrollHours']['total'] ?? 'PT0S');

                    // Calculate overtime from payroll hours
                    $dailyOT = $this->parseDuration($day['payrollHours']['dailyOvertime']['time'] ?? 'PT0S');
                    $dailyDoubleOT = $this->parseDuration($day['payrollHours']['dailyDoubleOvertime']['time'] ?? 'PT0S');
                    $totalOvertime = $dailyOT + $dailyDoubleOT;

                    HrmAttendanceDay::updateOrCreate(
                        [
                            'employee_id' => (int) $employee->id,
                            'date' => $date,
                        ],
                        [
                            'tracked_hours' => round($trackedTotal, 2),
                            'payroll_hours' => round($payrollTotal, 2),
                            'overtime_hours' => round($totalOvertime, 2),
                            'source' => 'jibble',
                            'jibble_data' => $day,
                            'notes' => null,
                        ]
                    );

                    $synced++;
                } catch (\Exception $e) {
                    Log::error('Failed to sync detailed timesheet', [
                        'employee_id' => $employee->id,
                        'date' => $date ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }
        }

        return $synced;
    }
}
