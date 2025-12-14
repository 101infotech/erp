<?php

namespace App\Console\Commands;

use App\Services\JibblePeopleService;
use Illuminate\Console\Command;

class SyncJibbleEmployees extends Command
{
    protected $signature = 'hrm:sync-jibble-employees';

    protected $description = 'Sync people from Jibble into the HRM employees table';

    public function handle(JibblePeopleService $peopleService): int
    {
        $this->info('Starting Jibble employee sync...');
        
        try {
            $count = $peopleService->syncPeople();
            $this->info("Successfully synced {$count} employees from Jibble.");
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync employees: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
