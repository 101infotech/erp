<?php

namespace Database\Seeders;

use App\Models\ServiceLead;
use App\Models\LeadStage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ServiceLeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user for assignment
        $admin = User::where('email', 'admin@example.com')->first() ?? User::first();

        // Get lead stages
        $stages = LeadStage::all();
        $leadCaptureStage = $stages->where('stage_number', 1)->first();
        $qualificationStage = $stages->where('stage_number', 2)->first();
        $siteVisitStage = $stages->where('stage_number', 3)->first();
        $designStage = $stages->where('stage_number', 4)->first();
        $bookingStage = $stages->where('stage_number', 5)->first();

        $leads = [
            [
                'service_requested' => 'Solar Panel Installation',
                'location' => 'Kathmandu, Nepal',
                'client_name' => 'Ramesh Sharma',
                'phone_number' => '+977-9841234567',
                'email' => 'ramesh.sharma@example.com',
                'status' => 'active',
                'lead_source' => 'Website',
                'lead_owner_id' => $admin?->id,
                'lead_stage_id' => $leadCaptureStage?->id,
                'priority' => 'high',
                'last_activity_at' => Carbon::now()->subDays(2),
                'next_follow_up_date' => Carbon::now()->addDays(1),
                'follow_up_count' => 2,
                'remarks' => 'Interested in 5kW solar system for residential property',
                'created_by' => $admin?->id,
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'service_requested' => 'Home Automation',
                'location' => 'Pokhara, Nepal',
                'client_name' => 'Sita Gurung',
                'phone_number' => '+977-9851234567',
                'email' => 'sita.gurung@example.com',
                'status' => 'active',
                'lead_source' => 'Referral',
                'lead_owner_id' => $admin?->id,
                'lead_stage_id' => $qualificationStage?->id,
                'priority' => 'medium',
                'last_activity_at' => Carbon::now()->subDays(5),
                'site_visit_scheduled_at' => Carbon::now()->addDays(3),
                'next_follow_up_date' => Carbon::now()->addDays(2),
                'follow_up_count' => 3,
                'remarks' => 'Looking for smart home automation for new villa',
                'created_by' => $admin?->id,
                'created_at' => Carbon::now()->subDays(7),
            ],
            [
                'service_requested' => 'CCTV System',
                'location' => 'Lalitpur, Nepal',
                'client_name' => 'Krishna Thapa',
                'phone_number' => '+977-9861234567',
                'email' => 'krishna.thapa@example.com',
                'status' => 'active',
                'lead_source' => 'Social Media',
                'lead_owner_id' => $admin?->id,
                'lead_stage_id' => $siteVisitStage?->id,
                'priority' => 'high',
                'last_activity_at' => Carbon::now()->subDays(1),
                'site_visit_scheduled_at' => Carbon::now()->addDays(1),
                'next_follow_up_date' => Carbon::now()->addDays(1),
                'follow_up_count' => 4,
                'remarks' => 'Needs 16-camera CCTV system for commercial building',
                'created_by' => $admin?->id,
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'service_requested' => 'Solar Water Heater',
                'location' => 'Bhaktapur, Nepal',
                'client_name' => 'Maya Rai',
                'phone_number' => '+977-9871234567',
                'email' => 'maya.rai@example.com',
                'status' => 'active',
                'lead_source' => 'Phone Call',
                'lead_owner_id' => $admin?->id,
                'lead_stage_id' => $designStage?->id,
                'priority' => 'medium',
                'last_activity_at' => Carbon::now()->subHours(12),
                'site_visit_completed_at' => Carbon::now()->subDays(2),
                'design_proposed_at' => Carbon::now()->subDays(1),
                'design_version' => 1,
                'quoted_amount' => 85000.00,
                'next_follow_up_date' => Carbon::now()->addDays(2),
                'follow_up_count' => 5,
                'remarks' => 'Design sent for approval. Waiting for client feedback',
                'created_by' => $admin?->id,
                'created_at' => Carbon::now()->subDays(15),
            ],
            [
                'service_requested' => 'Inverter Installation',
                'location' => 'Biratnagar, Nepal',
                'client_name' => 'Prakash Chaudhary',
                'phone_number' => '+977-9881234567',
                'email' => 'prakash.c@example.com',
                'status' => 'active',
                'lead_source' => 'Website',
                'lead_owner_id' => $admin?->id,
                'lead_stage_id' => $bookingStage?->id,
                'priority' => 'high',
                'last_activity_at' => Carbon::now()->subHours(6),
                'site_visit_completed_at' => Carbon::now()->subDays(5),
                'design_proposed_at' => Carbon::now()->subDays(3),
                'design_approved_at' => Carbon::now()->subDays(1),
                'design_version' => 2,
                'booking_confirmed_at' => Carbon::now()->subHours(8),
                'project_code' => 'INV-2026-001',
                'quoted_amount' => 125000.00,
                'advance_amount' => 50000.00,
                'paid_amount' => 50000.00,
                'payment_status' => 'partial',
                'payment_received_at' => Carbon::now()->subHours(8),
                'next_follow_up_date' => Carbon::now()->addDays(7),
                'follow_up_count' => 7,
                'remarks' => 'Advance paid. Installation scheduled for next week',
                'created_by' => $admin?->id,
                'created_at' => Carbon::now()->subDays(20),
            ],
            [
                'service_requested' => 'LED Lighting System',
                'location' => 'Patan, Nepal',
                'client_name' => 'Binita Shrestha',
                'phone_number' => '+977-9891234567',
                'email' => 'binita.shrestha@example.com',
                'status' => 'active',
                'lead_source' => 'Referral',
                'lead_owner_id' => $admin?->id,
                'lead_stage_id' => $leadCaptureStage?->id,
                'priority' => 'low',
                'last_activity_at' => Carbon::now()->subDays(4),
                'next_follow_up_date' => Carbon::now()->addDays(3),
                'follow_up_count' => 1,
                'remarks' => 'Initial inquiry. Need to schedule call',
                'created_by' => $admin?->id,
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'service_requested' => 'Electrical Wiring',
                'location' => 'Janakpur, Nepal',
                'client_name' => 'Suresh Jha',
                'phone_number' => '+977-9801234567',
                'email' => 'suresh.jha@example.com',
                'status' => 'active',
                'lead_source' => 'Website',
                'lead_owner_id' => $admin?->id,
                'lead_stage_id' => $qualificationStage?->id,
                'priority' => 'medium',
                'last_activity_at' => Carbon::now()->subDays(3),
                'site_visit_scheduled_at' => Carbon::now()->addDays(5),
                'next_follow_up_date' => Carbon::now()->addDays(1),
                'follow_up_count' => 2,
                'remarks' => 'Complete rewiring needed for 3-story building',
                'created_by' => $admin?->id,
                'created_at' => Carbon::now()->subDays(8),
            ],
            [
                'service_requested' => 'Solar Panel Installation',
                'location' => 'Dharan, Nepal',
                'client_name' => 'Anjali Limbu',
                'phone_number' => '+977-9811234567',
                'email' => 'anjali.limbu@example.com',
                'status' => 'active',
                'lead_source' => 'Social Media',
                'lead_owner_id' => $admin?->id,
                'lead_stage_id' => $siteVisitStage?->id,
                'priority' => 'high',
                'last_activity_at' => Carbon::now()->subHours(18),
                'site_visit_scheduled_at' => Carbon::now()->addDays(2),
                'site_visit_assigned_to_id' => $admin?->id,
                'next_follow_up_date' => Carbon::now()->addDays(2),
                'follow_up_count' => 4,
                'quoted_amount' => 250000.00,
                'remarks' => '10kW commercial solar installation. Site visit tomorrow',
                'created_by' => $admin?->id,
                'created_at' => Carbon::now()->subDays(12),
            ],
            [
                'service_requested' => 'Security System',
                'location' => 'Butwal, Nepal',
                'client_name' => 'Rajesh Paudel',
                'phone_number' => '+977-9821234567',
                'email' => 'rajesh.paudel@example.com',
                'status' => 'active',
                'lead_source' => 'Phone Call',
                'lead_owner_id' => $admin?->id,
                'lead_stage_id' => $leadCaptureStage?->id,
                'priority' => 'medium',
                'last_activity_at' => Carbon::now()->subHours(24),
                'next_follow_up_date' => Carbon::now()->addHours(48),
                'follow_up_count' => 1,
                'remarks' => 'Interested in complete security package with alarms and CCTV',
                'created_by' => $admin?->id,
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'service_requested' => 'Smart Lighting',
                'location' => 'Chitwan, Nepal',
                'client_name' => 'Geeta Adhikari',
                'phone_number' => '+977-9831234567',
                'email' => 'geeta.adhikari@example.com',
                'status' => 'active',
                'lead_source' => 'Referral',
                'lead_owner_id' => $admin?->id,
                'lead_stage_id' => $designStage?->id,
                'priority' => 'low',
                'last_activity_at' => Carbon::now()->subDays(6),
                'site_visit_completed_at' => Carbon::now()->subDays(4),
                'design_proposed_at' => Carbon::now()->subDays(2),
                'design_version' => 1,
                'quoted_amount' => 65000.00,
                'next_follow_up_date' => Carbon::now()->addDays(3),
                'follow_up_count' => 3,
                'remarks' => 'Proposal sent. Awaiting client decision',
                'created_by' => $admin?->id,
                'created_at' => Carbon::now()->subDays(18),
            ],
        ];

        foreach ($leads as $lead) {
            ServiceLead::create($lead);
        }

        $this->command->info('✓ Created ' . count($leads) . ' service leads');
    }
}
