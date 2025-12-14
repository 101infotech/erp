<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Contact forms table already has site_id
        // Booking forms table already has site_id
        // Just ensuring the relationship is properly set up

        // Add any additional fields if needed for specific sites
        Schema::table('contact_forms', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_forms', 'type')) {
                $table->string('type')->default('general')->after('site_id'); // general, support, inquiry
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_forms', function (Blueprint $table) {
            if (Schema::hasColumn('contact_forms', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
