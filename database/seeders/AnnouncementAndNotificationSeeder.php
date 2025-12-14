<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AnnouncementAndNotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Announcements
        DB::table('announcements')->insert([
            [
                'title' => 'System Maintenance',
                'content' => 'The ERP system will be under maintenance on Dec 15, 2025 from 10:00 PM to 12:00 AM.',
                'priority' => 'high',
                'recipient_type' => 'all',
                'recipient_ids' => null,
                'created_by' => 1,
                'send_email' => true,
                'is_published' => true,
                'published_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'New HR Policy',
                'content' => 'A new HR policy has been published. Please check the HRM module for details.',
                'priority' => 'normal',
                'recipient_type' => 'all',
                'recipient_ids' => null,
                'created_by' => 1,
                'send_email' => false,
                'is_published' => true,
                'published_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Notifications
        DB::table('notifications')->insert([
            [
                'user_id' => 1,
                'type' => 'leave_request',
                'title' => 'Leave Request Submitted',
                'message' => 'Your leave request for Dec 20-22 has been submitted.',
                'link' => '/leave/requests',
                'is_read' => false,
                'read_at' => null,
                'data' => json_encode(['days' => 3]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 1,
                'type' => 'complaint_submitted',
                'title' => 'Complaint Submitted',
                'message' => 'Your complaint regarding payroll has been received.',
                'link' => '/complaints',
                'is_read' => false,
                'read_at' => null,
                'data' => json_encode(['category' => 'payroll']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
