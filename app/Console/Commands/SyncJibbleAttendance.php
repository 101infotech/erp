<?php

namespace App\Console\Commands;

use App\Services\JibbleTimesheetService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncJibbleAttendance extends Command
{
    protected $signature = 'hrm:sync-jibble-attendance {--start=} {--end=}';

    protected $description = 'Sync attendance from Jibble daily summary into HRM attendance_days';

    public function handle(JibbleTimesheetService $timesheetService): int
    {
        $start = $this->option('start') ? Carbon::parse($this->option('start')) : Carbon::yesterday();
        $end = $this->option('end') ? Carbon::parse($this->option('end')) : Carbon::yesterday();

        $this->info("Starting Jibble attendance sync from {$start->toDateString()} to {$end->toDateString()}...");
        
        try {
            $count = $timesheetService->syncDailySummary($start, $end);
            $this->info("Successfully synced {$count} attendance day records.");
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync attendance: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
