<?php

namespace App\Services;

use App\Models\HrmCompany;
use App\Models\HrmDepartment;
use App\Models\HrmEmployee;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class JibblePeopleService
{
    public function __construct(private readonly JibbleAuthService $authService) {}

    public function fetchAll(array $select = [], array $queryOverrides = []): array
    {
        $token = $this->authService->getToken();
        $baseUrl = rtrim(config('services.jibble.base_url', ''), '/');
        $selectString = $select ? implode(',', $select) : config(
            'services.jibble.people_select',
            'id,email,code,fullName,managers'
        );

        if (!$baseUrl) {
            throw new RuntimeException('Jibble base URL not configured.');
        }

        $results = [];
        $skip = 0;
        $pageSize = 100;

        do {
            // OData parameters must use correct casing and boolean values as 'true'/'false' strings.
            $query = [
                '$select' => $selectString,
                '$expand' => 'group($select=id,name)',
                '$count' => 'true',
                '$orderby' => 'fullName',
                '$skip' => $skip,
                '$top' => $pageSize,
                ...$queryOverrides,
            ];

            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])
                ->withToken($token)
                ->get($baseUrl . '/People', $query);

            if ($response->failed()) {
                throw new RuntimeException('Unable to fetch Jibble people: ' . $response->body());
            }

            $batch = $response->json()['value'] ?? [];
            $results = array_merge($results, $batch);
            $skip += $pageSize;
        } while (count($batch) === $pageSize);

        return $results;
    }

    /**
     * Sync employees from Jibble to local database
     * 
     * @return int Number of employees synced
     */
    public function syncEmployees(): int
    {
        $people = $this->fetchAll();
        $synced = 0;

        foreach ($people as $person) {
            try {
                $companyId = $this->resolveCompanyId($person);
                $departmentId = $this->resolveDepartmentId($person, $companyId);
                $fullName = $person['fullName'] ?? trim(($person['firstName'] ?? '') . ' ' . ($person['lastName'] ?? ''));
                $email = $person['email'] ?? null;

                // Validate email uniqueness (allow multiple NULL, but not duplicate emails)
                if ($email) {
                    $existingEmployee = HrmEmployee::where('email', $email)
                        ->where('jibble_person_id', '!=', $person['id'])
                        ->first();

                    if ($existingEmployee) {
                        Log::warning('Duplicate email detected during Jibble sync', [
                            'jibble_id' => $person['id'],
                            'email' => $email,
                            'existing_employee_id' => $existingEmployee->id,
                        ]);
                        // Skip this email to prevent duplicates
                        $email = null;
                    }
                }

                // Find existing employee by jibble_person_id
                $employee = HrmEmployee::firstOrNew(['jibble_person_id' => $person['id']]);

                // If employee has email and no user_id, try to link or create user
                if ($email && !$employee->user_id) {
                    $user = \App\Models\User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name' => $fullName ?: 'Unknown',
                            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                        ]
                    );
                    $employee->user_id = $user->id;
                }

                $employee->company_id = $companyId;
                $employee->department_id = $departmentId;
                $employee->full_name = $fullName ?: 'Unknown';
                $employee->email = $email ?? $employee->email;
                $employee->phone = $person['phoneNumber'] ?? $employee->phone;
                $employee->status = $person['status'] ?? $employee->status ?? 'active';
                $employee->code = $employee->code
                    ?? $person['employeeCode']
                    ?? $person['staffId']
                    ?? Str::uuid()->toString();

                $employee->save();

                $synced++;
            } catch (\Exception $e) {
                Log::error('Failed to sync employee from Jibble', [
                    'jibble_person_id' => $person['id'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        return $synced;
    }

    private function resolveCompanyId(array $person): int
    {
        $configured = config('services.jibble.default_company_id');
        if ($configured) {
            return (int) $configured;
        }

        $existing = HrmCompany::first();
        if ($existing) {
            return $existing->id;
        }

        $companyName = $person['company']['name'] ?? $person['organization'] ?? 'Saubhagya Group';

        return HrmCompany::create([
            'name' => $companyName,
            'address' => $person['company']['address'] ?? null,
        ])->id;
    }

    private function resolveDepartmentId(array $person, int $companyId): ?int
    {
        $name = $person['department']['name'] ?? $person['department'] ?? null;

        if (!$name) {
            return null;
        }

        return HrmDepartment::firstOrCreate([
            'company_id' => $companyId,
            'name' => $name,
        ])->id;
    }

    /**
     * Create a new person in Jibble
     */
    public function createPerson(HrmEmployee $employee): ?string
    {
        $token = $this->authService->getToken();
        $baseUrl = rtrim(config('services.jibble.base_url', ''), '/');

        if (!$baseUrl) {
            throw new RuntimeException('Jibble base URL not configured.');
        }

        $payload = [
            'fullName' => $employee->full_name,
            'email' => $employee->email,
            'phoneNumber' => $employee->phone,
            'employeeCode' => $employee->code,
            'role' => 'Member',
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])
            ->withToken($token)
            ->post($baseUrl . '/People', $payload);

        if ($response->failed()) {
            Log::error('Failed to create person in Jibble', [
                'employee_id' => $employee->id,
                'error' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();
        return $data['id'] ?? null;
    }

    /**
     * Update a person in Jibble
     */
    public function updatePerson(HrmEmployee $employee): bool
    {
        if (!$employee->jibble_person_id) {
            // If no Jibble ID, try to create instead
            $jibbleId = $this->createPerson($employee);
            if ($jibbleId) {
                $employee->update(['jibble_person_id' => $jibbleId]);
                return true;
            }
            return false;
        }

        $token = $this->authService->getToken();
        $baseUrl = rtrim(config('services.jibble.base_url', ''), '/');

        if (!$baseUrl) {
            throw new RuntimeException('Jibble base URL not configured.');
        }

        $payload = [
            'fullName' => $employee->full_name,
            'email' => $employee->email,
            'phoneNumber' => $employee->phone,
            'employeeCode' => $employee->code,
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])
            ->withToken($token)
            ->patch($baseUrl . '/People/' . $employee->jibble_person_id, $payload);

        if ($response->failed()) {
            Log::error('Failed to update person in Jibble', [
                'employee_id' => $employee->id,
                'jibble_person_id' => $employee->jibble_person_id,
                'error' => $response->body(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Delete a person from Jibble
     */
    public function deletePerson(HrmEmployee $employee): bool
    {
        if (!$employee->jibble_person_id) {
            return true; // Nothing to delete in Jibble
        }

        $token = $this->authService->getToken();
        $baseUrl = rtrim(config('services.jibble.base_url', ''), '/');

        if (!$baseUrl) {
            throw new RuntimeException('Jibble base URL not configured.');
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])
            ->withToken($token)
            ->delete($baseUrl . '/People/' . $employee->jibble_person_id);

        if ($response->failed()) {
            Log::error('Failed to delete person from Jibble', [
                'employee_id' => $employee->id,
                'jibble_person_id' => $employee->jibble_person_id,
                'error' => $response->body(),
            ]);
            return false;
        }

        return true;
    }
}
