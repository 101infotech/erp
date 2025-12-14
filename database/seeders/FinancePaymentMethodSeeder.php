<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinanceCompany;
use App\Models\FinancePaymentMethod;

class FinancePaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = FinanceCompany::all();

        // Payment methods from Excel analysis
        $paymentMethods = [
            ['name' => 'Cash', 'type' => 'cash'],
            ['name' => 'Bank Transfer', 'type' => 'bank'],
            ['name' => 'Cheque', 'type' => 'cheque'],
            ['name' => 'Mobile Wallet', 'type' => 'mobile_wallet'],
        ];

        foreach ($companies as $company) {
            foreach ($paymentMethods as $method) {
                FinancePaymentMethod::create([
                    'company_id' => $company->id,
                    'name' => $method['name'],
                    'type' => $method['type'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
