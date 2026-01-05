<?php

namespace App\Console\Commands;

use App\Models\HrmPayrollRecord;
use App\Services\PayrollFinanceIntegrationService;
use Illuminate\Console\Command;

class SyncPayrollToFinance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:sync-payroll 
                            {--status=approved : Sync payrolls with this status (approved, paid, or all)}
                            {--from= : Start date in YYYY-MM-DD format}
                            {--to= : End date in YYYY-MM-DD format}
                            {--dry-run : Show what would be synced without actually syncing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync approved/paid payroll records to finance module as expense transactions';

    protected PayrollFinanceIntegrationService $financeIntegrationService;

    /**
     * Create a new command instance.
     */
    public function __construct(PayrollFinanceIntegrationService $financeIntegrationService)
    {
        parent::__construct();
        $this->financeIntegrationService = $financeIntegrationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting payroll to finance sync...');

        $status = $this->option('status');
        $isDryRun = $this->option('dry-run');

        // Build query
        $query = HrmPayrollRecord::with('employee');

        // Filter by status
        if ($status === 'all') {
            $query->whereIn('status', ['approved', 'paid']);
        } else {
            $query->where('status', $status);
        }

        // Filter by date range
        if ($this->option('from')) {
            $query->where('period_start_ad', '>=', $this->option('from'));
        }

        if ($this->option('to')) {
            $query->where('period_end_ad', '<=', $this->option('to'));
        }

        $payrolls = $query->get();

        if ($payrolls->isEmpty()) {
            $this->warn('No payroll records found matching the criteria.');
            return 0;
        }

        $this->info("Found {$payrolls->count()} payroll records to sync.");

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No data will be created');
            $this->table(
                ['ID', 'Employee', 'Period', 'Status', 'Amount'],
                $payrolls->map(fn($p) => [
                    $p->id,
                    $p->employee->name,
                    "{$p->period_start_bs} - {$p->period_end_bs}",
                    $p->status,
                    'रू ' . number_format($p->net_salary, 2),
                ])
            );
            return 0;
        }

        // Confirm before proceeding
        if (!$this->confirm('Do you want to proceed with syncing these payroll records to finance?')) {
            $this->info('Sync cancelled.');
            return 0;
        }

        $bar = $this->output->createProgressBar($payrolls->count());
        $bar->start();

        $successCount = 0;
        $failedCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($payrolls as $payroll) {
            try {
                // Check if already synced
                $exists = \App\Models\FinanceTransaction::where('reference_number', "PAYROLL-{$payroll->id}")->exists();

                if ($exists) {
                    $skippedCount++;
                    $this->newLine();
                    $this->comment("Skipped: Payroll #{$payroll->id} already has a finance transaction");
                } else {
                    $transaction = $this->financeIntegrationService->createFinanceTransactionForPayroll($payroll);

                    if ($transaction) {
                        $successCount++;
                    } else {
                        $failedCount++;
                        $errors[] = "Payroll #{$payroll->id}: Failed to create transaction (check logs)";
                    }
                }
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = "Payroll #{$payroll->id}: " . $e->getMessage();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Show summary
        $this->info('Sync completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Success', $successCount],
                ['Failed', $failedCount],
                ['Skipped (Already Synced)', $skippedCount],
                ['Total', $payrolls->count()],
            ]
        );

        if (!empty($errors)) {
            $this->error('Errors encountered:');
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
        }

        return $failedCount > 0 ? 1 : 0;
    }
}
