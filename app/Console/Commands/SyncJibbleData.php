<?php

namespace App\Console\Commands;

use App\Services\JibblePeopleService;
use App\Services\JibbleTimesheetService;
use App\Services\JibbleTimeTrackingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncJibbleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jibble:sync 
                            {--days=7 : Number of days to sync (default: 7)}
                            {--employees : Sync employee data}
                            {--attendance : Sync attendance data}
                            {--all : Sync all data (employees + attendance)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from Jibble (employees, attendance summaries, and time entries)';

    /**
     * Execute the console command.
     */
    public function handle(
        JibblePeopleService $peopleService,
        JibbleTimesheetService $timesheetService,
        JibbleTimeTrackingService $timeTrackingService
    ): int {
        $this->info('Starting Jibble sync...');

        $syncEmployees = $this->option('employees') || $this->option('all');
        $syncAttendance = $this->option('attendance') || $this->option('all');

        // If no specific option is provided, sync all
        if (!$syncEmployees && !$syncAttendance) {
            $syncEmployees = true;
            $syncAttendance = true;
        }

        try {
            // Sync employee data
            if ($syncEmployees) {
                $this->info('Syncing employee data...');
                $employeeCount = $peopleService->syncEmployees();
                $this->info("✓ Synced {$employeeCount} employees");
            }

            // Sync attendance data
            if ($syncAttendance) {
                $days = (int) $this->option('days');
                $endDate = Carbon::today()->toDateString();
                $startDate = Carbon::today()->subDays($days - 1)->toDateString();

                $this->info("Syncing attendance from {$startDate} to {$endDate}...");

                // Sync daily summaries (tracked hours, payroll, overtime)
                $summaryCount = $timesheetService->syncDailySummary($startDate, $endDate);
                $this->info("✓ Synced {$summaryCount} attendance records");

                // Sync time entries (clock in/out details)
                $entriesCount = $timeTrackingService->syncTimeEntries($startDate, $endDate);
                $this->info("✓ Synced {$entriesCount} time entries");
            }

            $this->info('Jibble sync completed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
