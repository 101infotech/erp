<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sites = [
            [
                'name' => 'Saubhagya Group',
                'slug' => 'saubhagya-group',
                'domain' => 'saubhagyagroup.com',
                'description' => 'Main corporate website for Saubhagya Group',
                'is_active' => true,
            ],
            [
                'name' => 'Brand Bird Agency',
                'slug' => 'brand-bird-agency',
                'domain' => 'brandbirdagency.com',
                'description' => 'Marketing and branding agency website',
                'is_active' => true,
            ],
            [
                'name' => 'Saubhagya Ghar',
                'slug' => 'saubhagya-ghar',
                'domain' => 'saubhagyaghar.com',
                'description' => 'Real estate and property website',
                'is_active' => true,
            ],
            [
                'name' => 'Saubhagya Estimate',
                'slug' => 'saubhagya-estimate',
                'domain' => 'saubhagyaestimate.com',
                'description' => 'Estimation and costing services website',
                'is_active' => true,
            ],
        ];

        foreach ($sites as $site) {
            Site::create($site);
        }
    }
}
