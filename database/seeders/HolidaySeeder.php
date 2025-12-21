<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holiday;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            [
                'name' => 'New Year',
                'date' => '2026-01-01',
                'description' => "New Year's Day",
                'is_company_wide' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Labour Day',
                'date' => '2026-05-01',
                'description' => 'Labour Day',
                'is_company_wide' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Company Day',
                'date' => now()->addDays(7)->toDateString(),
                'description' => 'Company founding day',
                'is_company_wide' => true,
                'is_active' => true,
            ],
        ];

        foreach ($holidays as $h) {
            Holiday::updateOrCreate(
                ['name' => $h['name'], 'date' => $h['date']],
                $h
            );
        }
    }
}
