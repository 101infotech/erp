<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if data already exists
        if (DB::table('finance_transactions')->count() > 0) {
            $this->command->warn('Finance data already exists. Skipping seeding.');
            $this->command->info('Run: php artisan db:seed --class=FinanceModuleSeeder --force to reseed');
            return;
        }

        // Get first company or use ID 1
        $companyId = DB::table('finance_companies')->first()->id ?? 1;

        // If no company exists, create one
        if (!DB::table('finance_companies')->where('id', $companyId)->exists()) {
            $this->command->info('Creating default company...');
            DB::table('finance_companies')->insert([
                'name' => 'Default Company',
                'type' => 'holding',
                'parent_company_id' => null,
                'contact_email' => 'info@company.com.np',
                'contact_phone' => '+977-1-4567890',
                'pan_number' => 'PAN123456789',
                'address' => 'Kathmandu, Nepal',
                'established_date_bs' => '2080-04-01',
                'fiscal_year_start_month' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $companyId = DB::getPdo()->lastInsertId();
        }

        $this->command->info("Using Company ID: $companyId");

        // 1. Create Bank Accounts (only if none exist for this company)
        $companies = [
            [
                'name' => 'Tech Solutions Pvt. Ltd.',
                'type' => 'holding',
                'parent_company_id' => null,
                'contact_email' => 'info@techsolutions.com.np',
                'contact_phone' => '+977-1-4567890',
                'pan_number' => 'PAN123456789',
                'address' => 'Kathmandu, Nepal',
                'established_date_bs' => '2080-04-01',
                'fiscal_year_start_month' => 4, // Shrawan
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Digital Services Nepal',
                'type' => 'sister',
                'parent_company_id' => null,
                'contact_email' => 'contact@digitalservices.com.np',
                'contact_phone' => '+977-1-9876543',
                'pan_number' => 'PAN987654321',
                'address' => 'Lalitpur, Nepal',
                'established_date_bs' => '2080-05-15',
                'fiscal_year_start_month' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($companies as $company) {
            DB::table('finance_companies')->insert($company);
        }

        // 2. Create Bank Accounts
        $bankAccounts = [
            [
                'company_id' => 1,
                'account_name' => 'Nepal Bank Ltd - Current',
                'bank_name' => 'Nepal Bank Limited',
                'branch_name' => 'New Road Branch',
                'account_number' => '01234567890',
                'account_type' => 'current',
                'currency' => 'NPR',
                'opening_balance' => 5000000.00,
                'current_balance' => 5000000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'account_name' => 'Himalayan Bank - Savings',
                'bank_name' => 'Himalayan Bank',
                'branch_name' => 'Durbar Marg',
                'account_number' => '09876543210',
                'account_type' => 'savings',
                'currency' => 'NPR',
                'opening_balance' => 2500000.00,
                'current_balance' => 2500000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($bankAccounts as $account) {
            DB::table('finance_bank_accounts')->insert($account);
        }

        // 3. Create Founders
        $founders = [
            [
                'name' => 'Ram Kumar Sharma',
                'email' => 'ram@techsolutions.com.np',
                'phone' => '+977-9841234567',
                'pan_number' => 'PAN601234567',
                'citizenship_number' => '123-456-789',
                'address' => 'Kathmandu-15, Nepal',
                'ownership_percentage' => 60.00,
                'is_active' => true,
                'joined_date_bs' => '2080-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sita Devi Thapa',
                'email' => 'sita@techsolutions.com.np',
                'phone' => '+977-9851234567',
                'pan_number' => 'PAN401234567',
                'citizenship_number' => '987-654-321',
                'address' => 'Lalitpur-12, Nepal',
                'ownership_percentage' => 40.00,
                'is_active' => true,
                'joined_date_bs' => '2080-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($founders as $founder) {
            DB::table('finance_founders')->insert($founder);
        }

        // 4. Create Accounts (Chart of Accounts)
        $accounts = [
            // Assets
            ['company_id' => 1, 'account_code' => '1000', 'account_name' => 'Cash', 'account_type' => 'asset', 'parent_account_id' => null, 'description' => 'Cash on hand', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '1100', 'account_name' => 'Bank Accounts', 'account_type' => 'asset', 'parent_account_id' => null, 'description' => 'Bank balances', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '1200', 'account_name' => 'Accounts Receivable', 'account_type' => 'asset', 'parent_account_id' => null, 'description' => 'Money owed by customers', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '1500', 'account_name' => 'Fixed Assets', 'account_type' => 'asset', 'parent_account_id' => null, 'description' => 'Property and equipment', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Liabilities
            ['company_id' => 1, 'account_code' => '2000', 'account_name' => 'Accounts Payable', 'account_type' => 'liability', 'parent_account_id' => null, 'description' => 'Money owed to vendors', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '2100', 'account_name' => 'Credit Card', 'account_type' => 'liability', 'parent_account_id' => null, 'description' => 'Credit card payables', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '2500', 'account_name' => 'Loans Payable', 'account_type' => 'liability', 'parent_account_id' => null, 'description' => 'Outstanding loans', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Revenue
            ['company_id' => 1, 'account_code' => '4000', 'account_name' => 'Sales Revenue', 'account_type' => 'revenue', 'parent_account_id' => null, 'description' => 'Revenue from sales', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '4100', 'account_name' => 'Service Revenue', 'account_type' => 'revenue', 'parent_account_id' => null, 'description' => 'Revenue from services', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '4200', 'account_name' => 'Other Income', 'account_type' => 'revenue', 'parent_account_id' => null, 'description' => 'Miscellaneous income', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Expenses
            ['company_id' => 1, 'account_code' => '5000', 'account_name' => 'Cost of Goods Sold', 'account_type' => 'expense', 'parent_account_id' => null, 'description' => 'Direct costs', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '5100', 'account_name' => 'Salaries & Wages', 'account_type' => 'expense', 'parent_account_id' => null, 'description' => 'Employee compensation', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '5200', 'account_name' => 'Rent Expense', 'account_type' => 'expense', 'parent_account_id' => null, 'description' => 'Office rent', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '5300', 'account_name' => 'Utilities', 'account_type' => 'expense', 'parent_account_id' => null, 'description' => 'Electricity, water, internet', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '5400', 'account_name' => 'Marketing & Advertising', 'account_type' => 'expense', 'parent_account_id' => null, 'description' => 'Marketing costs', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '5500', 'account_name' => 'Office Supplies', 'account_type' => 'expense', 'parent_account_id' => null, 'description' => 'Office materials', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Equity
            ['company_id' => 1, 'account_code' => '3000', 'account_name' => 'Owner\'s Equity', 'account_type' => 'equity', 'parent_account_id' => null, 'description' => 'Owner capital', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'account_code' => '3100', 'account_name' => 'Retained Earnings', 'account_type' => 'equity', 'parent_account_id' => null, 'description' => 'Accumulated profits', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($accounts as $account) {
            DB::table('finance_accounts')->insert($account);
        }

        // 5. Create Categories
        $categories = [
            ['company_id' => 1, 'name' => 'Software Development', 'type' => 'income', 'parent_category_id' => null, 'color_code' => '#10b981', 'icon' => 'code', 'is_system' => false, 'display_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'name' => 'Consulting Services', 'type' => 'income', 'parent_category_id' => null, 'color_code' => '#3b82f6', 'icon' => 'briefcase', 'is_system' => false, 'display_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'name' => 'Maintenance & Support', 'type' => 'income', 'parent_category_id' => null, 'color_code' => '#8b5cf6', 'icon' => 'tool', 'is_system' => false, 'display_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['company_id' => 1, 'name' => 'Salaries', 'type' => 'expense', 'parent_category_id' => null, 'color_code' => '#ef4444', 'icon' => 'users', 'is_system' => false, 'display_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'name' => 'Office Rent', 'type' => 'expense', 'parent_category_id' => null, 'color_code' => '#f59e0b', 'icon' => 'home', 'is_system' => false, 'display_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'name' => 'Internet & Utilities', 'type' => 'expense', 'parent_category_id' => null, 'color_code' => '#06b6d4', 'icon' => 'zap', 'is_system' => false, 'display_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'name' => 'Software Licenses', 'type' => 'expense', 'parent_category_id' => null, 'color_code' => '#6366f1', 'icon' => 'package', 'is_system' => false, 'display_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'name' => 'Marketing', 'type' => 'expense', 'parent_category_id' => null, 'color_code' => '#ec4899', 'icon' => 'megaphone', 'is_system' => false, 'display_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($categories as $category) {
            DB::table('finance_categories')->insert($category);
        }

        // 6. Create Payment Methods
        $paymentMethods = [
            ['company_id' => 1, 'name' => 'Cash', 'type' => 'cash', 'icon' => 'cash', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'name' => 'Bank Transfer', 'type' => 'bank_transfer', 'icon' => 'bank', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'name' => 'Cheque', 'type' => 'check', 'icon' => 'file-text', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'name' => 'Credit Card', 'type' => 'credit_card', 'icon' => 'credit-card', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_id' => 1, 'name' => 'eSewa', 'type' => 'online', 'icon' => 'smartphone', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($paymentMethods as $method) {
            DB::table('finance_payment_methods')->insert($method);
        }

        // 7. Create Customers
        $customers = [
            [
                'company_id' => 1,
                'customer_code' => 'CUST-001',
                'name' => 'ABC Corporation',
                'pan_number' => 'PAN111222333',
                'contact_person' => 'John Sharma',
                'email' => 'accounts@abccorp.com',
                'phone' => '+977-1-4445555',
                'address' => 'Thamel, Kathmandu',
                'customer_type' => 'corporate',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'customer_code' => 'CUST-002',
                'name' => 'XYZ Industries',
                'pan_number' => 'PAN444555666',
                'contact_person' => 'Sarah Thapa',
                'email' => 'finance@xyzind.com',
                'phone' => '+977-1-5556666',
                'address' => 'Patan, Lalitpur',
                'customer_type' => 'corporate',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'customer_code' => 'CUST-003',
                'name' => 'Smart Solutions Ltd.',
                'pan_number' => 'PAN777888999',
                'contact_person' => 'Ram Bahadur',
                'email' => 'contact@smartsolutions.com',
                'phone' => '+977-1-7778888',
                'address' => 'Bhaktapur',
                'customer_type' => 'corporate',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($customers as $customer) {
            DB::table('finance_customers')->insert($customer);
        }

        // 8. Create Vendors
        $vendors = [
            [
                'company_id' => 1,
                'vendor_code' => 'VEND-001',
                'name' => 'Hardware Suppliers Nepal',
                'pan_number' => 'PAN123123123',
                'contact_person' => 'Mohan Karki',
                'email' => 'sales@hardwaresuppliers.com',
                'phone' => '+977-1-1112222',
                'address' => 'New Road, Kathmandu',
                'vendor_type' => 'supplier',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'vendor_code' => 'VEND-002',
                'name' => 'Office Furniture Co.',
                'pan_number' => 'PAN456456456',
                'contact_person' => 'Krishna Paudel',
                'email' => 'info@officefurniture.com',
                'phone' => '+977-1-3334444',
                'address' => 'Pulchowk, Lalitpur',
                'vendor_type' => 'supplier',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($vendors as $vendor) {
            DB::table('finance_vendors')->insert($vendor);
        }

        // 9. Create Transactions (Last 6 months with BS dates)
        $transactions = [];
        $txnNumber = 1;

        // Current fiscal year in BS (2081)
        $fiscalYear = '2081';

        // Generate transactions for months 4-9 (Shrawan to Poush - Jul to Dec)
        for ($month = 4; $month <= 9; $month++) {
            $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);

            // Income transactions (5-8 per month)
            for ($i = 0; $i < rand(5, 8); $i++) {
                $day = rand(1, 28);
                $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                $dateBS = "$fiscalYear-$monthStr-$dayStr";

                $transactions[] = [
                    'company_id' => 1,
                    'transaction_number' => 'TXN-' . str_pad($txnNumber++, 6, '0', STR_PAD_LEFT),
                    'transaction_date_bs' => $dateBS,
                    'transaction_type' => 'income',
                    'category_id' => rand(1, 3),
                    'subcategory_id' => null,
                    'reference_type' => 'sales',
                    'reference_id' => null,
                    'description' => 'Service revenue - Project delivery',
                    'amount' => rand(50000, 500000),
                    'debit_account_id' => 2, // Bank Account (asset increases)
                    'credit_account_id' => 8, // Sales Revenue (revenue increases)
                    'payment_method' => 'bank_transfer',
                    'payment_reference' => 'REF-' . rand(1000, 9999),
                    'handled_by_user_id' => 1,
                    'received_paid_by' => 'Customer',
                    'is_from_holding_company' => false,
                    'fund_source_company_id' => null,
                    'bill_number' => null,
                    'invoice_number' => 'INV-' . str_pad($txnNumber, 6, '0', STR_PAD_LEFT),
                    'document_path' => null,
                    'status' => 'approved',
                    'approved_by_user_id' => 1,
                    'approved_at' => now(),
                    'fiscal_year_bs' => $fiscalYear,
                    'fiscal_month_bs' => $month,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Expense transactions (8-12 per month)
            for ($i = 0; $i < rand(8, 12); $i++) {
                $day = rand(1, 28);
                $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                $dateBS = "$fiscalYear-$monthStr-$dayStr";

                $expenseCategories = [4, 5, 6, 7, 8]; // Expense category IDs
                $categoryId = $expenseCategories[array_rand($expenseCategories)];

                $transactions[] = [
                    'company_id' => 1,
                    'transaction_number' => 'TXN-' . str_pad($txnNumber++, 6, '0', STR_PAD_LEFT),
                    'transaction_date_bs' => $dateBS,
                    'transaction_type' => 'expense',
                    'category_id' => $categoryId,
                    'subcategory_id' => null,
                    'reference_type' => 'purchase',
                    'reference_id' => null,
                    'description' => 'Monthly operating expense',
                    'amount' => rand(10000, 150000),
                    'debit_account_id' => rand(11, 16), // Expense account (expense increases)
                    'credit_account_id' => 2, // Bank Account (asset decreases)
                    'payment_method' => rand(0, 1) ? 'bank_transfer' : 'cash',
                    'payment_reference' => 'PAY-' . rand(1000, 9999),
                    'handled_by_user_id' => 1,
                    'received_paid_by' => 'Vendor',
                    'is_from_holding_company' => false,
                    'fund_source_company_id' => null,
                    'bill_number' => 'BILL-' . str_pad($txnNumber, 6, '0', STR_PAD_LEFT),
                    'invoice_number' => null,
                    'document_path' => null,
                    'status' => 'approved',
                    'approved_by_user_id' => 1,
                    'approved_at' => now(),
                    'fiscal_year_bs' => $fiscalYear,
                    'fiscal_month_bs' => $month,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach ($transactions as $transaction) {
            DB::table('finance_transactions')->insert($transaction);
        }

        // 10. Create Sales (Invoices)
        $sales = [];
        $saleNumber = 1;
        for ($month = 4; $month <= 9; $month++) {
            for ($i = 0; $i < 3; $i++) { // 3 sales per month = 18 total
                $day = rand(1, 28);
                $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
                $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                $dateBS = "$fiscalYear-$monthStr-$dayStr";

                $taxableAmount = rand(100000, 800000);
                $vatAmount = $taxableAmount * 0.13;
                $totalAmount = $taxableAmount + $vatAmount;
                $isPaid = rand(0, 10) > 2; // 80% paid

                $sales[] = [
                    'company_id' => 1,
                    'sale_number' => 'SALE-' . str_pad($saleNumber++, 6, '0', STR_PAD_LEFT),
                    'sale_date_bs' => $dateBS,
                    'customer_id' => rand(1, 3),
                    'customer_name' => null,
                    'customer_pan' => null,
                    'customer_address' => null,
                    'invoice_number' => 'INV-2081-' . str_pad($saleNumber, 4, '0', STR_PAD_LEFT),
                    'total_amount' => $totalAmount,
                    'vat_amount' => $vatAmount,
                    'taxable_amount' => $taxableAmount,
                    'discount_amount' => 0,
                    'net_amount' => $totalAmount,
                    'payment_status' => $isPaid ? 'paid' : 'pending',
                    'payment_method' => $isPaid ? 'bank_transfer' : null,
                    'payment_date_bs' => $isPaid ? $dateBS : null,
                    'description' => 'Software development and consulting services',
                    'fiscal_year_bs' => $fiscalYear,
                    'document_path' => null,
                    'created_by_user_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach ($sales as $sale) {
            DB::table('finance_sales')->insert($sale);
        }

        // 11. Create Purchases (Bills)
        $purchases = [];
        $purchaseNumber = 1;
        for ($month = 4; $month <= 9; $month++) {
            for ($i = 0; $i < 2; $i++) { // 2 purchases per month = 12 total
                $day = rand(1, 28);
                $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
                $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                $dateBS = "$fiscalYear-$monthStr-$dayStr";

                $taxableAmount = rand(50000, 400000);
                $vatAmount = $taxableAmount * 0.13;
                $tdsPercentage = 1.5;
                $tdsAmount = $taxableAmount * ($tdsPercentage / 100);
                $netAmount = $taxableAmount + $vatAmount - $tdsAmount;
                $isPaid = rand(0, 10) > 3; // 70% paid

                $purchases[] = [
                    'company_id' => 1,
                    'purchase_number' => 'PURCH-' . str_pad($purchaseNumber++, 6, '0', STR_PAD_LEFT),
                    'purchase_date_bs' => $dateBS,
                    'vendor_id' => rand(1, 2),
                    'vendor_name' => null,
                    'vendor_pan' => null,
                    'vendor_address' => null,
                    'bill_number' => 'BILL-2081-' . str_pad($purchaseNumber, 4, '0', STR_PAD_LEFT),
                    'total_amount' => $taxableAmount + $vatAmount,
                    'vat_amount' => $vatAmount,
                    'tds_amount' => $tdsAmount,
                    'tds_percentage' => $tdsPercentage,
                    'taxable_amount' => $taxableAmount,
                    'discount_amount' => 0,
                    'net_amount' => $netAmount,
                    'payment_status' => $isPaid ? 'paid' : 'pending',
                    'payment_method' => $isPaid ? 'bank_transfer' : null,
                    'payment_date_bs' => $isPaid ? $dateBS : null,
                    'description' => 'Office supplies and equipment purchase',
                    'fiscal_year_bs' => $fiscalYear,
                    'document_path' => null,
                    'created_by_user_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        foreach ($purchases as $purchase) {
            DB::table('finance_purchases')->insert($purchase);
        }

        $this->command->info('Finance module seeded successfully!');
        $this->command->info('- Companies: 2');
        $this->command->info('- Bank Accounts: 2');
        $this->command->info('- Founders: 2');
        $this->command->info('- Chart of Accounts: 18');
        $this->command->info('- Categories: 8');
        $this->command->info('- Payment Methods: 5');
        $this->command->info('- Customers: 3');
        $this->command->info('- Vendors: 2');
        $this->command->info('- Transactions: ' . count($transactions));
        $this->command->info('- Sales: 15');
        $this->command->info('- Purchases: 12');
    }
}
