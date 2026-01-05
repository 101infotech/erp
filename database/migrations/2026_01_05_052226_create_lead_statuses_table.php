<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lead_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_key')->unique();
            $table->string('display_name');
            $table->string('color_class');
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('priority');
            $table->index('is_active');
        });

        // Insert default statuses
        DB::table('lead_statuses')->insert([
            [
                'status_key' => 'Intake',
                'display_name' => 'Intake',
                'color_class' => 'bg-blue-50 text-blue-700 border-blue-200',
                'priority' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_key' => 'Contacted',
                'display_name' => 'Contacted',
                'color_class' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                'priority' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_key' => 'Inspection Booked',
                'display_name' => 'Booked',
                'color_class' => 'bg-green-50 text-green-700 border-green-200',
                'priority' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_key' => 'Inspection Rescheduled',
                'display_name' => 'Rescheduled',
                'color_class' => 'bg-orange-50 text-orange-700 border-orange-200',
                'priority' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_key' => 'Office Visit Requested',
                'display_name' => 'Office Visit',
                'color_class' => 'bg-purple-50 text-purple-700 border-purple-200',
                'priority' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_key' => 'Reports Sent',
                'display_name' => 'Reports Sent',
                'color_class' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'priority' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_key' => 'Bad Lead',
                'display_name' => 'Bad Lead',
                'color_class' => 'bg-red-50 text-red-700 border-red-200',
                'priority' => 7,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_key' => 'Out of Valley',
                'display_name' => 'Out of Valley',
                'color_class' => 'bg-gray-50 text-gray-700 border-gray-200',
                'priority' => 8,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_key' => 'Cancelled',
                'display_name' => 'Cancelled',
                'color_class' => 'bg-red-50 text-red-700 border-red-200',
                'priority' => 9,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status_key' => 'Positive',
                'display_name' => 'Positive',
                'color_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'priority' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_statuses');
    }
};
