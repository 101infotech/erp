<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinanceCompany;
use App\Models\FinanceAccount;

class FinanceAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = FinanceCompany::all();

        // Standard Chart of Accounts (Nepal-specific)
        $chartOfAccounts = [
            // Assets (1000-1999)
            ['code' => '1000', 'name' => 'Cash in Hand', 'type' => 'asset', 'parent' => null],
            ['code' => '1100', 'name' => 'Bank Accounts', 'type' => 'asset', 'parent' => null],
            ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'asset', 'parent' => null],
            ['code' => '1300', 'name' => 'Inventory', 'type' => 'asset', 'parent' => null],
            ['code' => '1400', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'parent' => null],
            ['code' => '1500', 'name' => 'Fixed Assets', 'type' => 'asset', 'parent' => null],
            ['code' => '1510', 'name' => 'Land & Building', 'type' => 'asset', 'parent' => '1500'],
            ['code' => '1520', 'name' => 'Furniture & Fixtures', 'type' => 'asset', 'parent' => '1500'],
            ['code' => '1530', 'name' => 'Vehicles', 'type' => 'asset', 'parent' => '1500'],
            ['code' => '1540', 'name' => 'Equipment', 'type' => 'asset', 'parent' => '1500'],
            ['code' => '1600', 'name' => 'Accumulated Depreciation', 'type' => 'asset', 'parent' => null],

            // Liabilities (2000-2999)
            ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'liability', 'parent' => null],
            ['code' => '2100', 'name' => 'VAT Payable', 'type' => 'liability', 'parent' => null],
            ['code' => '2110', 'name' => 'TDS Payable', 'type' => 'liability', 'parent' => null],
            ['code' => '2200', 'name' => 'Salary Payable', 'type' => 'liability', 'parent' => null],
            ['code' => '2300', 'name' => 'Loan Payable', 'type' => 'liability', 'parent' => null],
            ['code' => '2400', 'name' => 'Intercompany Payable', 'type' => 'liability', 'parent' => null],

            // Equity (3000-3999)
            ['code' => '3000', 'name' => 'Owner Capital', 'type' => 'equity', 'parent' => null],
            ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'equity', 'parent' => null],
            ['code' => '3200', 'name' => 'Drawings', 'type' => 'equity', 'parent' => null],

            // Revenue (4000-4999)
            ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue', 'parent' => null],
            ['code' => '4100', 'name' => 'Service Revenue', 'type' => 'revenue', 'parent' => null],
            ['code' => '4200', 'name' => 'Construction Revenue', 'type' => 'revenue', 'parent' => null],
            ['code' => '4300', 'name' => 'Design Revenue', 'type' => 'revenue', 'parent' => null],
            ['code' => '4400', 'name' => 'Rental Income', 'type' => 'revenue', 'parent' => null],
            ['code' => '4900', 'name' => 'Other Income', 'type' => 'revenue', 'parent' => null],

            // Expenses (5000-5999)
            ['code' => '5000', 'name' => 'Salary & Wages', 'type' => 'expense', 'parent' => null],
            ['code' => '5100', 'name' => 'Marketing & Advertising', 'type' => 'expense', 'parent' => null],
            ['code' => '5200', 'name' => 'Office Rent', 'type' => 'expense', 'parent' => null],
            ['code' => '5300', 'name' => 'Utilities', 'type' => 'expense', 'parent' => null],
            ['code' => '5400', 'name' => 'Office Supplies', 'type' => 'expense', 'parent' => null],
            ['code' => '5500', 'name' => 'Construction Materials', 'type' => 'expense', 'parent' => null],
            ['code' => '5600', 'name' => 'Professional Fees', 'type' => 'expense', 'parent' => null],
            ['code' => '5700', 'name' => 'Travel & Transport', 'type' => 'expense', 'parent' => null],
            ['code' => '5800', 'name' => 'Depreciation Expense', 'type' => 'expense', 'parent' => null],
            ['code' => '5900', 'name' => 'Miscellaneous', 'type' => 'expense', 'parent' => null],
        ];

        foreach ($companies as $company) {
            $accountMap = [];

            foreach ($chartOfAccounts as $account) {
                $parentAccountId = null;

                if ($account['parent']) {
                    // Find parent account ID
                    $parentAccountId = $accountMap[$account['parent']] ?? null;
                }

                $createdAccount = FinanceAccount::create([
                    'company_id' => $company->id,
                    'account_code' => $account['code'],
                    'account_name' => $account['name'],
                    'account_type' => $account['type'],
                    'parent_account_id' => $parentAccountId,
                    'is_active' => true,
                ]);

                $accountMap[$account['code']] = $createdAccount->id;
            }
        }
    }
}
