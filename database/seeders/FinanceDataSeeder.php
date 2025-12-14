<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinanceDataSeeder extends Seeder
{
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('finance_transactions')->count() > 0) {
            $this->command->warn('Finance transaction data already exists.');
            return;
        }

        // Get first company
        $company = DB::table('finance_companies')->first();
        if (!$company) {
            $this->command->error('No companies found.');
            return;
        }

        $companyId = $company->id;
        $this->command->info("Seeding for company: {$company->name}");

        $transactions = [];
        $sales = [];
        $purchases = [];

        $txnNum = 1;
        $saleNum = 1;
        $purchNum = 1;

        $customerIds = DB::table('finance_customers')->where('company_id', $companyId)->pluck('id')->toArray();
        $vendorIds = DB::table('finance_vendors')->where('company_id', $companyId)->pluck('id')->toArray();

        for ($month = 4; $month <= 9; $month++) {
            // Income transactions - 6 per month
            for ($i = 0; $i < 6; $i++) {
                $date = sprintf('2081-%02d-%02d', $month, rand(1, 28));
                $amount = rand(100000, 500000);

                $transactions[] = [
                    'company_id' => $companyId,
                    'transaction_number' => sprintf('TXN-%06d', $txnNum++),
                    'transaction_date_bs' => $date,
                    'transaction_type' => 'income',
                    'description' => 'Service revenue',
                    'amount' => $amount,
                    'payment_method' => 'bank_transfer',
                    'fiscal_year_bs' => '2081',
                    'fiscal_month_bs' => $month,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Expense transactions - 10 per month
            for ($i = 0; $i < 10; $i++) {
                $date = sprintf('2081-%02d-%02d', $month, rand(1, 28));
                $amount = rand(20000, 150000);

                $transactions[] = [
                    'company_id' => $companyId,
                    'transaction_number' => sprintf('TXN-%06d', $txnNum++),
                    'transaction_date_bs' => $date,
                    'transaction_type' => 'expense',
                    'description' => 'Operating expense',
                    'amount' => $amount,
                    'payment_method' => 'bank_transfer',
                    'fiscal_year_bs' => '2081',
                    'fiscal_month_bs' => $month,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Sales - 3 per month
            for ($i = 0; $i < 3; $i++) {
                $date = sprintf('2081-%02d-%02d', $month, rand(1, 28));
                $taxable = rand(150000, 700000);
                $vat = $taxable * 0.13;
                $total = $taxable + $vat;
                $isPaid = rand(0, 10) > 2;

                $sales[] = [
                    'company_id' => $companyId,
                    'sale_number' => sprintf('SALE-%06d', $saleNum++),
                    'sale_date_bs' => $date,
                    'customer_id' => !empty($customerIds) ? $customerIds[array_rand($customerIds)] : null,
                    'customer_name' => 'Customer ' . rand(100, 999),
                    'invoice_number' => sprintf('INV-2081-%04d', $saleNum),
                    'total_amount' => $total,
                    'vat_amount' => $vat,
                    'taxable_amount' => $taxable,
                    'net_amount' => $total,
                    'payment_status' => $isPaid ? 'paid' : 'pending',
                    'payment_method' => $isPaid ? 'bank_transfer' : null,
                    'payment_date_bs' => $isPaid ? $date : null,
                    'description' => 'Software services',
                    'fiscal_year_bs' => '2081',
                    'created_by_user_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Purchases - 2 per month
            for ($i = 0; $i < 2; $i++) {
                $date = sprintf('2081-%02d-%02d', $month, rand(1, 28));
                $taxable = rand(80000, 350000);
                $vat = $taxable * 0.13;
                $tds = $taxable * 0.015;
                $net = $taxable + $vat - $tds;
                $isPaid = rand(0, 10) > 3;

                $purchases[] = [
                    'company_id' => $companyId,
                    'purchase_number' => sprintf('PURCH-%06d', $purchNum++),
                    'purchase_date_bs' => $date,
                    'vendor_id' => !empty($vendorIds) ? $vendorIds[array_rand($vendorIds)] : null,
                    'vendor_name' => 'Vendor ' . rand(100, 999),
                    'bill_number' => sprintf('BILL-2081-%04d', $purchNum),
                    'total_amount' => $taxable + $vat,
                    'vat_amount' => $vat,
                    'tds_amount' => $tds,
                    'tds_percentage' => 1.5,
                    'taxable_amount' => $taxable,
                    'net_amount' => $net,
                    'payment_status' => $isPaid ? 'paid' : 'pending',
                    'payment_method' => $isPaid ? 'bank_transfer' : null,
                    'payment_date_bs' => $isPaid ? $date : null,
                    'description' => 'Equipment purchase',
                    'fiscal_year_bs' => '2081',
                    'created_by_user_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert in batches
        foreach (array_chunk($transactions, 50) as $chunk) {
            DB::table('finance_transactions')->insert($chunk);
        }
        foreach (array_chunk($sales, 50) as $chunk) {
            DB::table('finance_sales')->insert($chunk);
        }
        foreach (array_chunk($purchases, 50) as $chunk) {
            DB::table('finance_purchases')->insert($chunk);
        }

        $this->command->info('✓ Created ' . count($transactions) . ' transactions');
        $this->command->info('✓ Created ' . count($sales) . ' sales');
        $this->command->info('✓ Created ' . count($purchases) . ' purchases');
    }
}
