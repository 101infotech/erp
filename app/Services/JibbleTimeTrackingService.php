<?php

namespace App\Services;

use App\Models\HrmEmployee;
use App\Models\HrmTimeEntry;
use App\Models\HrmAttendanceDay;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class JibbleTimeTrackingService
{
    public function __construct(private readonly JibbleAuthService $authService) {}

    /**
     * Fetch raw time entries from Jibble for a specific date
     */
    public function fetchTimeEntries(Carbon|string $date, array $personIds = []): array
    {
        $token = $this->authService->getToken();
        $url = 'https://time-tracking.prod.jibble.io/v1/TimeEntries';

        $dateString = Carbon::parse($date)->toDateString();

        $query = [
            '$count' => 'true',
            '$expand' => 'activity($select=id,name,code),project($select=id,name,code),person($select=id,groupId,fullName)',
            '$filter' => "(belongsToDate eq {$dateString} and status ne 'Archived')",
            '$orderby' => 'time asc',
            '$skip' => 0,
            '$top' => 100,
            '$select' => 'id,type,time,localTime,belongsToDate,personId,projectId,activityId,locationId,note,coordinates,address',
        ];

        try {
            $response = Http::timeout(120)
                ->withToken($token)
                ->get($url, $query);

            if ($response->failed()) {
                Log::error('Jibble TimeEntries API failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new RuntimeException('Failed to fetch time entries from Jibble API. Status: ' . $response->status());
            }

            $data = $response->json();
            $allEntries = $data['value'] ?? [];

            // Filter by person IDs if specified
            if (!empty($personIds)) {
                $allEntries = array_filter($allEntries, function ($entry) use ($personIds) {
                    return in_array($entry['personId'] ?? null, $personIds);
                });
            }

            return $allEntries;
        } catch (\Exception $e) {
            Log::error('Jibble time entries fetch failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Fetch time entries for a date range
     */
    public function fetchTimeEntriesRange(Carbon|string $startDate, Carbon|string $endDate, array $personIds = []): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $allEntries = [];

        $current = $start->copy();
        while ($current->lte($end)) {
            try {
                $entries = $this->fetchTimeEntries($current, $personIds);
                $allEntries = array_merge($allEntries, $entries);
            } catch (\Exception $e) {
                Log::warning('Failed to fetch time entries for date', [
                    'date' => $current->toDateString(),
                    'error' => $e->getMessage()
                ]);
            }
            $current->addDay();
        }

        return $allEntries;
    }

    /**
     * Sync time entries from Jibble to database
     */
    public function syncTimeEntries(Carbon|string $startDate, Carbon|string $endDate, array $personIds = []): int
    {
        $entries = $this->fetchTimeEntriesRange($startDate, $endDate, $personIds);
        $synced = 0;

        foreach ($entries as $entry) {
            try {
                $processed = $this->processTimeEntry($entry);
                if ($processed) {
                    $synced++;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to process time entry', [
                    'entry_id' => $entry['id'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Link time entries to attendance days
        $this->linkTimeEntriesToAttendanceDays($startDate, $endDate);

        return $synced;
    }

    /**
     * Link time entries to their corresponding attendance days
     */
    private function linkTimeEntriesToAttendanceDays(Carbon|string $startDate, Carbon|string $endDate): void
    {
        $startDateString = Carbon::parse($startDate)->toDateString();
        $endDateString = Carbon::parse($endDate)->toDateString();

        // Fetch all unlinked time entries for the date range
        $timeEntries = HrmTimeEntry::whereBetween('belongs_to_date', [$startDateString, $endDateString])
            ->whereNull('attendance_day_id')
            ->get();

        if ($timeEntries->isEmpty()) {
            return;
        }

        // Fetch all attendance days for the date range in bulk
        $attendanceDays = HrmAttendanceDay::whereBetween('date', [$startDateString, $endDateString])
            ->get()
            ->keyBy(function ($day) {
                return $day->employee_id . '_' . $day->date;
            });

        // Link entries to attendance days using bulk update
        $updates = [];
        foreach ($timeEntries as $entry) {
            $key = $entry->employee_id . '_' . $entry->belongs_to_date;
            if (isset($attendanceDays[$key])) {
                $updates[] = [
                    'id' => $entry->id,
                    'attendance_day_id' => $attendanceDays[$key]->id
                ];
            }
        }

        // Perform bulk update
        if (!empty($updates)) {
            foreach ($updates as $update) {
                HrmTimeEntry::where('id', $update['id'])
                    ->update(['attendance_day_id' => $update['attendance_day_id']]);
            }
        }
    }

    /**
     * Process and store a single time entry
     */
    private function processTimeEntry(array $entry): bool
    {
        $jibblePersonId = $entry['personId'] ?? null;
        if (!$jibblePersonId) {
            return false;
        }

        $employee = HrmEmployee::where('jibble_person_id', $jibblePersonId)->first();
        if (!$employee) {
            Log::warning('Employee not found for time entry', ['jibble_person_id' => $jibblePersonId]);
            return false;
        }

        $belongsToDate = $entry['belongsToDate'] ?? null;
        if (!$belongsToDate) {
            return false;
        }

        // Find or create attendance day
        $attendanceDay = HrmAttendanceDay::where('employee_id', $employee->id)
            ->where('date', $belongsToDate)
            ->first();

        $timeEntryData = [
            'employee_id' => $employee->id,
            'attendance_day_id' => $attendanceDay?->id,
            'type' => $entry['type'] ?? 'In',
            'time' => Carbon::parse($entry['time']),
            'local_time' => Carbon::parse($entry['localTime']),
            'belongs_to_date' => $belongsToDate,
            'project_id' => $entry['projectId'] ?? null,
            'project_name' => $entry['project']['name'] ?? null,
            'activity_id' => $entry['activityId'] ?? null,
            'activity_name' => $entry['activity']['name'] ?? null,
            'location_id' => $entry['locationId'] ?? null,
            'note' => $entry['note'] ?? null,
            'address' => $entry['address'] ?? null,
            'latitude' => $entry['coordinates']['latitude'] ?? null,
            'longitude' => $entry['coordinates']['longitude'] ?? null,
            'raw_data' => $entry,
        ];

        HrmTimeEntry::updateOrCreate(
            ['jibble_entry_id' => $entry['id']],
            $timeEntryData
        );

        return true;
    }

    /**
     * Get time entries for a specific employee and date
     */
    public function getEmployeeTimeEntries(int $employeeId, Carbon|string $date): array
    {
        return HrmTimeEntry::where('employee_id', $employeeId)
            ->where('belongs_to_date', Carbon::parse($date)->toDateString())
            ->orderBy('time', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Calculate work duration from time entries for a day
     */
    public function calculateDailyDuration(int $employeeId, Carbon|string $date): array
    {
        $entries = HrmTimeEntry::where('employee_id', $employeeId)
            ->where('belongs_to_date', Carbon::parse($date)->toDateString())
            ->orderBy('time', 'asc')
            ->get();

        $totalMinutes = 0;
        $clockIns = [];
        $clockOuts = [];

        foreach ($entries as $entry) {
            if ($entry->type === 'In') {
                $clockIns[] = $entry;
            } else {
                $clockOuts[] = $entry;
            }
        }

        // Pair clock-ins with clock-outs
        $sessions = [];
        for ($i = 0; $i < count($clockIns); $i++) {
            if (isset($clockOuts[$i])) {
                $duration = $clockIns[$i]->time->diffInMinutes($clockOuts[$i]->time);
                $totalMinutes += $duration;
                $sessions[] = [
                    'clock_in' => $clockIns[$i]->local_time,
                    'clock_out' => $clockOuts[$i]->local_time,
                    'duration_minutes' => $duration,
                ];
            }
        }

        return [
            'total_hours' => round($totalMinutes / 60, 2),
            'total_minutes' => $totalMinutes,
            'sessions' => $sessions,
            'clock_ins' => count($clockIns),
            'clock_outs' => count($clockOuts),
        ];
    }
}
