<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinanceCompany;

class FinanceCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Holding Company
        $holdingCompany = FinanceCompany::create([
            'name' => 'Saubhagya Group',
            'type' => 'holding',
            'parent_company_id' => null,
            'fiscal_year_start_month' => 4, // Shrawan 1 (BS month 4)
            'pan_number' => '000000000',
            'address' => 'Kathmandu, Nepal',
            'contact_phone' => '+977-1-4000000',
            'contact_email' => 'info@saubhagyagroup.com',
            'is_active' => true,
        ]);

        // Create Sister Companies
        $sisterCompanies = [
            [
                'name' => 'Saubhagya Construction',
                'pan_number' => '111111111',
                'contact_email' => 'construction@saubhagyagroup.com',
            ],
            [
                'name' => 'Brand Bird',
                'pan_number' => '222222222',
                'contact_email' => 'brandbird@saubhagyagroup.com',
            ],
            [
                'name' => 'Saubhagya Ghar',
                'pan_number' => '333333333',
                'contact_email' => 'ghar@saubhagyagroup.com',
            ],
            [
                'name' => 'SSIT',
                'pan_number' => '444444444',
                'contact_email' => 'ssit@saubhagyagroup.com',
            ],
            [
                'name' => 'Your Hostel',
                'pan_number' => '555555555',
                'contact_email' => 'yourhostel@saubhagyagroup.com',
            ],
        ];

        foreach ($sisterCompanies as $company) {
            FinanceCompany::create([
                'name' => $company['name'],
                'type' => 'sister',
                'parent_company_id' => $holdingCompany->id,
                'fiscal_year_start_month' => 4, // Same as holding
                'pan_number' => $company['pan_number'],
                'address' => 'Kathmandu, Nepal',
                'contact_phone' => '+977-1-4000000',
                'contact_email' => $company['contact_email'],
                'is_active' => true,
            ]);
        }
    }
}
