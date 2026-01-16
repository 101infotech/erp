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
        Schema::create('lead_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->date('follow_up_date');
            $table->enum('follow_up_type', ['call', 'visit', 'whatsapp', 'email', 'sms']);
            $table->string('follow_up_outcome')->nullable();
            $table->text('follow_up_notes')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->unsignedBigInteger('follow_up_owner_id');
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('service_leads')->onDelete('cascade');
            $table->foreign('follow_up_owner_id')->references('id')->on('users');

            $table->index(['lead_id', 'follow_up_date']);
            $table->index('follow_up_owner_id');
            $table->index('follow_up_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_follow_ups');
    }
};
