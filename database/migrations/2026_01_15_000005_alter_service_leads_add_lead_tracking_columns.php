<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds new columns to service_leads to support enhanced lead lifecycle
     */
    public function up(): void
    {
        Schema::table('service_leads', function (Blueprint $table) {
            // Lead source and ownership
            $table->string('lead_source', 100)->nullable()->after('service_requested');
            $table->unsignedBigInteger('lead_owner_id')->nullable()->after('lead_source');
            $table->unsignedBigInteger('lead_stage_id')->default(1)->after('lead_owner_id');

            // Priority and lead status tracking
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('lead_stage_id');
            $table->timestamp('last_activity_at')->nullable()->after('priority');

            // Site visit tracking
            $table->datetime('site_visit_scheduled_at')->nullable()->after('last_activity_at');
            $table->datetime('site_visit_completed_at')->nullable()->after('site_visit_scheduled_at');
            $table->unsignedBigInteger('site_visit_assigned_to_id')->nullable()->after('site_visit_completed_at');
            $table->text('site_visit_observations')->nullable()->after('site_visit_assigned_to_id');

            // Design and proposal tracking
            $table->datetime('design_proposed_at')->nullable()->after('site_visit_observations');
            $table->integer('design_version')->default(0)->after('design_proposed_at');
            $table->datetime('design_approved_at')->nullable()->after('design_version');
            $table->text('design_notes')->nullable()->after('design_approved_at');

            // Booking and project confirmation
            $table->datetime('booking_confirmed_at')->nullable()->after('design_notes');
            $table->string('project_code', 50)->nullable()->after('booking_confirmed_at');

            // Payment tracking
            $table->decimal('quoted_amount', 15, 2)->nullable()->after('project_code');
            $table->decimal('advance_amount', 15, 2)->default(0)->after('quoted_amount');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('advance_amount');
            $table->enum('payment_status', ['pending', 'partial', 'full'])->default('pending')->after('paid_amount');
            $table->datetime('payment_received_at')->nullable()->after('payment_status');

            // Follow-up tracking
            $table->datetime('next_follow_up_date')->nullable()->after('payment_received_at');
            $table->integer('follow_up_count')->default(0)->after('next_follow_up_date');

            // Closure tracking
            $table->enum('closure_reason', ['converted', 'won', 'lost', 'no_interest', 'budget_constraint', 'other'])->nullable()->after('follow_up_count');
            $table->datetime('closed_at')->nullable()->after('closure_reason');
            $table->text('closure_notes')->nullable()->after('closed_at');

            // Add foreign keys
            $table->foreign('lead_owner_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('lead_stage_id')->references('id')->on('lead_stages');
            $table->foreign('site_visit_assigned_to_id')->references('id')->on('users')->nullOnDelete();

            // Add indexes for performance
            $table->index('lead_owner_id');
            $table->index('lead_stage_id');
            $table->index('priority');
            $table->index('payment_status');
            $table->index(['lead_owner_id', 'lead_stage_id']);
            $table->index(['priority', 'lead_stage_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_leads', function (Blueprint $table) {
            // Drop foreign keys
            try {
                $table->dropForeign(['lead_owner_id']);
            } catch (\Exception $e) {}
            
            try {
                $table->dropForeign(['lead_stage_id']);
            } catch (\Exception $e) {}
            
            try {
                $table->dropForeign(['site_visit_assigned_to_id']);
            } catch (\Exception $e) {}

            // Drop indexes
            try {
                $table->dropIndex(['lead_owner_id']);
            } catch (\Exception $e) {}
            
            try {
                $table->dropIndex(['lead_stage_id']);
            } catch (\Exception $e) {}
            
            try {
                $table->dropIndex(['priority']);
            } catch (\Exception $e) {}
            
            try {
                $table->dropIndex(['payment_status']);
            } catch (\Exception $e) {}
            
            try {
                $table->dropIndex(['lead_owner_id', 'lead_stage_id']);
            } catch (\Exception $e) {}
            
            try {
                $table->dropIndex(['priority', 'lead_stage_id']);
            } catch (\Exception $e) {}

            $table->dropColumn([
                'lead_source',
                'lead_owner_id',
                'lead_stage_id',
                'priority',
                'last_activity_at',
                'site_visit_scheduled_at',
                'site_visit_completed_at',
                'site_visit_assigned_to_id',
                'site_visit_observations',
                'design_proposed_at',
                'design_version',
                'design_approved_at',
                'design_notes',
                'booking_confirmed_at',
                'project_code',
                'quoted_amount',
                'advance_amount',
                'paid_amount',
                'payment_status',
                'payment_received_at',
                'next_follow_up_date',
                'follow_up_count',
                'closure_reason',
                'closed_at',
                'closure_notes',
            ]);
        });
    }
};
