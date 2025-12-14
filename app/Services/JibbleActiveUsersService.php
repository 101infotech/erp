<?php

namespace App\Services;

use App\Models\HrmEmployee;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class JibbleActiveUsersService
{
    public function __construct(private readonly JibbleAuthService $authService) {}

    /**
     * Fetch currently active (clocked in) people from Jibble
     * Uses the time-tracking API to get people with ongoing sessions
     * 
     * @return array Array of people who are currently clocked in
     */
    public function fetchActivePeople(): array
    {
        $token = $this->authService->getToken();

        try {
            // Get all time entries for today
            $today = now()->toDateString();

            $response = Http::timeout(15)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->withToken($token)
                ->get('https://time-tracking.prod.jibble.io/v1/TimeEntries', [
                    // OData date literal format (no quotes, no cast)
                    '$filter' => "belongsToDate eq {$today}",
                    '$select' => 'id,personId,type,time,belongsToDate',
                    '$expand' => 'person($select=id,fullName;$expand=group($select=id,name))',
                    '$orderby' => 'time desc',
                    '$top' => 200,
                ]);

            if ($response->failed()) {
                Log::error('Jibble Active Sessions API failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new RuntimeException('Failed to fetch active sessions from Jibble API. Status: ' . $response->status());
            }

            $data = $response->json();
            $timeEntries = $data['value'] ?? [];

            // Track clock in/out status for each person
            $peopleStatus = [];

            foreach ($timeEntries as $entry) {
                $personId = $entry['personId'] ?? null;
                $type = $entry['type'] ?? null;
                $person = $entry['person'] ?? null;

                if (!$personId || !$type || !$person) {
                    continue;
                }

                // Initialize person if not seen
                if (!isset($peopleStatus[$personId])) {
                    $peopleStatus[$personId] = [
                        'id' => $person['id'] ?? null,
                        'fullName' => $person['fullName'] ?? 'Unknown',
                        'group' => $person['group'] ?? null,
                        'isClockedIn' => false,
                        'lastClockIn' => null,
                    ];
                }

                // Track latest clock status (entries ordered by time desc, so first entry is latest)
                if (in_array($type, ['In', 'ClockIn']) && !$peopleStatus[$personId]['lastClockIn']) {
                    $peopleStatus[$personId]['isClockedIn'] = true;
                    $peopleStatus[$personId]['lastClockIn'] = $entry['time'] ?? null;
                } elseif (in_array($type, ['Out', 'ClockOut']) && !$peopleStatus[$personId]['lastClockIn']) {
                    // Latest event is clock out, so they're not active
                    $peopleStatus[$personId]['isClockedIn'] = false;
                }
            }

            // Return only people who are currently clocked in
            $activePeople = [];
            foreach ($peopleStatus as $status) {
                if ($status['isClockedIn']) {
                    $activePeople[] = [
                        'id' => $status['id'],
                        'fullName' => $status['fullName'],
                        'group' => $status['group'],
                        'isActive' => true,
                        'lastClockIn' => $status['lastClockIn'],
                    ];
                }
            }

            return $activePeople;
        } catch (\Exception $e) {
            Log::error('Failed to fetch active people from Jibble', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get currently active people with employee mapping
     * 
     * @return array Array of active employees with their details
     */
    public function getActiveEmployees(): array
    {
        $activePeople = $this->fetchActivePeople();
        $activeEmployees = [];

        foreach ($activePeople as $person) {
            $jibblePersonId = $person['id'] ?? null;

            if (!$jibblePersonId) {
                continue;
            }

            $employee = HrmEmployee::where('jibble_person_id', $jibblePersonId)->first();

            $activeEmployees[] = [
                'jibble_person_id' => $jibblePersonId,
                'full_name' => $person['fullName'] ?? 'Unknown',
                'email' => $person['email'] ?? null,
                'group' => $person['group'] ?? null,
                'is_active' => $person['isActive'] ?? false,
                'lastClockIn' => $person['lastClockIn'] ?? null,
                'employee_id' => $employee?->id,
                'employee_code' => $employee?->code,
                'employee' => $employee,
            ];
        }

        return $activeEmployees;
    }

    /**
     * Get cached active employees (updates every 2 minutes)
     * 
     * @return array
     */
    public function getCachedActiveEmployees(): array
    {
        return Cache::remember('jibble.active_employees', 120, function () {
            return $this->getActiveEmployees();
        });
    }

    /**
     * Clear the active employees cache
     */
    public function clearCache(): void
    {
        Cache::forget('jibble.active_employees');
    }
}
