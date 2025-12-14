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
        Schema::table('hrm_time_entries', function (Blueprint $table) {
            // Change type from enum to varchar to support all Jibble entry types
            // Jibble types: ClockIn, ClockOut, StartBreak, EndBreak, etc.
            $table->string('type', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hrm_time_entries', function (Blueprint $table) {
            // Revert back to enum
            $table->enum('type', ['In', 'Out'])->change();
        });
    }
};
