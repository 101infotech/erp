<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@saubhagyagroup.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Seed sites and other data
        $this->call([
            SiteSeeder::class,
            AnnouncementAndNotificationSeeder::class,

            // Finance Module Seeders
            FinanceCompanySeeder::class,
            FinanceCategorySeeder::class,
            FinancePaymentMethodSeeder::class,
            FinanceAccountSeeder::class,
        ]);
    }
}
