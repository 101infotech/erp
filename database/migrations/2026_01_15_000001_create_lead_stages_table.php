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
        Schema::create('lead_stages', function (Blueprint $table) {
            $table->id();
            $table->integer('stage_number')->unique();
            $table->string('stage_name', 100);
            $table->text('description')->nullable();
            $table->integer('auto_timeout_days')->nullable();
            $table->boolean('requires_action')->default(true);
            $table->string('notification_template')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('stage_number');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_stages');
    }
};
