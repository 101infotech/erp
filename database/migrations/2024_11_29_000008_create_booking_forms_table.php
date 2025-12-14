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
        Schema::create('booking_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->string('company_name')->nullable();
            $table->string('website_url')->nullable();
            $table->string('industry')->nullable();
            $table->json('services_interested')->nullable();
            $table->string('investment_range')->nullable();
            $table->string('flight_timeline')->nullable();
            $table->text('marketing_goals')->nullable();
            $table->text('current_challenges')->nullable();
            $table->string('ip_address')->nullable();
            $table->enum('status', ['new', 'contacted', 'scheduled', 'completed', 'cancelled'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_forms');
    }
};
