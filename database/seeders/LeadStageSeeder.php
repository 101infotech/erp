<?php

namespace Database\Seeders;

use App\Models\LeadStage;
use Illuminate\Database\Seeder;

class LeadStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'stage_number' => 1,
                'stage_name' => 'Lead Capture',
                'description' => 'New lead captured from website, referral, or marketing channel',
                'auto_timeout_days' => 30,
                'requires_action' => true,
                'notification_template' => 'lead_captured',
                'is_active' => true,
            ],
            [
                'stage_number' => 2,
                'stage_name' => 'Lead Qualification',
                'description' => 'Lead contacted and information verified. Initial qualification complete.',
                'auto_timeout_days' => 15,
                'requires_action' => true,
                'notification_template' => 'lead_qualified',
                'is_active' => true,
            ],
            [
                'stage_number' => 3,
                'stage_name' => 'Site Visit Scheduled',
                'description' => 'Site visit appointment scheduled with the client',
                'auto_timeout_days' => 7,
                'requires_action' => true,
                'notification_template' => 'site_visit_scheduled',
                'is_active' => true,
            ],
            [
                'stage_number' => 4,
                'stage_name' => 'Site Visit Completed',
                'description' => 'Site inspection completed and observations documented',
                'auto_timeout_days' => 10,
                'requires_action' => true,
                'notification_template' => 'site_visit_completed',
                'is_active' => true,
            ],
            [
                'stage_number' => 5,
                'stage_name' => 'Design & Proposal',
                'description' => 'Design proposal prepared and presented to client for approval',
                'auto_timeout_days' => 14,
                'requires_action' => true,
                'notification_template' => 'design_proposed',
                'is_active' => true,
            ],
            [
                'stage_number' => 6,
                'stage_name' => 'Booking Confirmed',
                'description' => 'Client approved design and confirmed booking with advance payment',
                'auto_timeout_days' => 5,
                'requires_action' => false,
                'notification_template' => 'booking_confirmed',
                'is_active' => true,
            ],
            [
                'stage_number' => 7,
                'stage_name' => 'Project Active',
                'description' => 'Project work started, full payment received or payment plan agreed',
                'auto_timeout_days' => null,
                'requires_action' => false,
                'notification_template' => 'project_active',
                'is_active' => true,
            ],
            [
                'stage_number' => 8,
                'stage_name' => 'Project Completion',
                'description' => 'Project work completed, final payment collected',
                'auto_timeout_days' => 7,
                'requires_action' => true,
                'notification_template' => 'project_completed',
                'is_active' => true,
            ],
            [
                'stage_number' => 9,
                'stage_name' => 'Closed',
                'description' => 'Lead converted to project or lost. Final stage in lifecycle.',
                'auto_timeout_days' => null,
                'requires_action' => false,
                'notification_template' => 'lead_closed',
                'is_active' => true,
            ],
        ];

        foreach ($stages as $stage) {
            LeadStage::firstOrCreate(
                ['stage_number' => $stage['stage_number']],
                $stage
            );
        }
    }
}
