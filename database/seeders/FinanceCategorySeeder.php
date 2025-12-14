<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinanceCompany;
use App\Models\FinanceCategory;

class FinanceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = FinanceCompany::all();

        // System-wide categories (from Excel analysis)
        $systemCategories = [
            // Income Categories
            ['name' => 'Sales Revenue', 'type' => 'income'],
            ['name' => 'Service Revenue', 'type' => 'income'],
            ['name' => 'Construction Revenue', 'type' => 'income'],
            ['name' => 'Design Revenue', 'type' => 'income'],
            ['name' => 'Rental Income', 'type' => 'income'],
            ['name' => 'Other Income', 'type' => 'income'],

            // Expense Categories (from Excel sheets)
            ['name' => 'Salary & Wages', 'type' => 'expense'],
            ['name' => 'Marketing & Advertising', 'type' => 'expense'],
            ['name' => 'Operational Expenses', 'type' => 'expense'],
            ['name' => 'Office Supplies', 'type' => 'expense'],
            ['name' => 'Construction Materials', 'type' => 'expense'],
            ['name' => 'Municipal Fees', 'type' => 'expense'],
            ['name' => 'Design & Development', 'type' => 'expense'],
            ['name' => 'Rent & Utilities', 'type' => 'expense'],
            ['name' => 'Travel & Transport', 'type' => 'expense'],
            ['name' => 'Professional Fees', 'type' => 'expense'],
            ['name' => 'Maintenance & Repairs', 'type' => 'expense'],
            ['name' => 'Insurance', 'type' => 'expense'],
            ['name' => 'Bank Charges', 'type' => 'expense'],
            ['name' => 'Depreciation', 'type' => 'expense'],
            ['name' => 'Miscellaneous', 'type' => 'expense'],

            // Both types
            ['name' => 'Transfer', 'type' => 'both'],
        ];

        foreach ($companies as $company) {
            foreach ($systemCategories as $category) {
                FinanceCategory::create([
                    'company_id' => $company->id,
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'parent_category_id' => null,
                    'is_system' => true,
                    'is_active' => true,
                ]);
            }
        }
    }
}
