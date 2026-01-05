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
        // First, alter the enum column to include new values
        DB::statement("ALTER TABLE finance_companies MODIFY COLUMN type ENUM('holding', 'sister', 'subsidiary', 'independent') DEFAULT 'independent'");

        // Then update any existing 'sister' type to 'subsidiary'
        DB::table('finance_companies')
            ->where('type', 'sister')
            ->update(['type' => 'subsidiary']);

        // Finally, remove 'sister' from the enum
        DB::statement("ALTER TABLE finance_companies MODIFY COLUMN type ENUM('holding', 'subsidiary', 'independent') DEFAULT 'independent'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the enum column back to old values
        DB::statement("ALTER TABLE finance_companies MODIFY COLUMN type ENUM('holding', 'sister') DEFAULT 'sister'");

        // Update any 'subsidiary' or 'independent' back to 'sister'
        DB::table('finance_companies')
            ->whereIn('type', ['subsidiary', 'independent'])
            ->update(['type' => 'sister']);
    }
};
