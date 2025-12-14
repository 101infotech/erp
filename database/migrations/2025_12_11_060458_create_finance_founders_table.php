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
        Schema::create('finance_founders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('citizenship_number')->nullable();
            $table->text('address')->nullable();
            $table->decimal('ownership_percentage', 5, 2)->nullable()->comment('Percentage of ownership');
            $table->boolean('is_active')->default(true);
            $table->string('joined_date_bs', 10)->nullable()->comment('YYYY-MM-DD format');
            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_founders');
    }
};
