<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;
use App\Models\CalendarEvent;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample projects based on the screenshots
        $projects = [
            [
                'name' => 'Decem App',
                'category' => 'Finance',
                'category_color' => '#4169E1',
                'completed_tasks' => 988,
                'total_tasks' => 1200,
                'budget' => 391991,
                'description' => 'Financial management application',
                'status' => 'active',
            ],
            [
                'name' => 'SkyLux',
                'category' => 'Education',
                'category_color' => '#FF7F3F',
                'completed_tasks' => 12,
                'total_tasks' => 50,
                'budget' => 51792,
                'description' => 'Educational platform',
                'status' => 'active',
            ],
            [
                'name' => 'DushMash',
                'category' => 'Finance',
                'category_color' => '#C74B9D',
                'completed_tasks' => 32,
                'total_tasks' => 100,
                'budget' => 31955,
                'description' => 'Financial dashboard',
                'status' => 'active',
            ],
            [
                'name' => 'Biofarm',
                'category' => 'Healthcare',
                'category_color' => '#3EAF7C',
                'completed_tasks' => 19,
                'total_tasks' => 75,
                'budget' => 11538,
                'description' => 'Healthcare management system',
                'status' => 'active',
            ],
            [
                'name' => 'PAD move',
                'category' => 'Travel',
                'category_color' => '#E74C3C',
                'completed_tasks' => 35,
                'total_tasks' => 120,
                'budget' => 21688,
                'description' => 'Travel booking platform',
                'status' => 'active',
            ],
            [
                'name' => 'Getstats',
                'category' => 'Logistics',
                'category_color' => '#17A2B8',
                'completed_tasks' => 88,
                'total_tasks' => 200,
                'budget' => 92581,
                'description' => 'Logistics tracking system',
                'status' => 'active',
            ],
        ];

        // Get or create users for team members
        $users = User::all();
        if ($users->isEmpty()) {
            // Create sample users if none exist
            $users = collect([
                User::create([
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'password' => bcrypt('password'),
                ]),
                User::create([
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'password' => bcrypt('password'),
                ]),
                User::create([
                    'name' => 'Bob Johnson',
                    'email' => 'bob@example.com',
                    'password' => bcrypt('password'),
                ]),
                User::create([
                    'name' => 'Alice Williams',
                    'email' => 'alice@example.com',
                    'password' => bcrypt('password'),
                ]),
            ]);
        }

        foreach ($projects as $projectData) {
            $project = Project::create($projectData);

            // Attach random team members to each project
            $randomUsers = $users->random(rand(2, min(4, $users->count())));
            foreach ($randomUsers as $user) {
                $project->members()->attach($user->id, ['role' => 'member']);
            }

            // Create some calendar events for this project
            for ($i = 0; $i < rand(3, 8); $i++) {
                CalendarEvent::create([
                    'project_id' => $project->id,
                    'event_date' => Carbon::now()->addDays(rand(1, 30)),
                    'title' => 'Project Meeting',
                    'description' => 'Team sync for ' . $project->name,
                    'priority' => ['low', 'normal', 'high'][rand(0, 2)],
                    'team_members' => $randomUsers->pluck('id')->toArray(),
                ]);
            }
        }
    }
}
