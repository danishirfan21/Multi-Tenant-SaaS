<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $acmeTenant = Tenant::where('slug', 'acme-corp')->first();

        if (!$acmeTenant) {
            $this->command->warn('Acme tenant not found. Run TenantSeeder first.');
            return;
        }

        $owner = User::where('tenant_id', $acmeTenant->id)
            ->where('role', 'owner')
            ->first();

        $admin = User::where('tenant_id', $acmeTenant->id)
            ->where('role', 'admin')
            ->first();

        $user = User::where('tenant_id', $acmeTenant->id)
            ->where('role', 'user')
            ->first();

        // Project 1: Website Redesign
        $project1 = Project::create([
            'tenant_id' => $acmeTenant->id,
            'user_id' => $owner->id,
            'name' => 'Website Redesign',
            'description' => 'Complete redesign of company website with modern UI/UX',
            'status' => 'active',
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(30),
        ]);

        Task::create([
            'tenant_id' => $acmeTenant->id,
            'project_id' => $project1->id,
            'user_id' => $admin->id,
            'title' => 'Design new homepage mockup',
            'description' => 'Create high-fidelity mockup for the new homepage',
            'status' => 'done',
            'priority' => 'high',
            'due_date' => now()->subDays(5),
            'order' => 1,
        ]);

        Task::create([
            'tenant_id' => $acmeTenant->id,
            'project_id' => $project1->id,
            'user_id' => $user->id,
            'title' => 'Implement responsive navigation',
            'description' => 'Build mobile-first navigation component',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => now()->addDays(3),
            'order' => 2,
        ]);

        Task::create([
            'tenant_id' => $acmeTenant->id,
            'project_id' => $project1->id,
            'user_id' => $user->id,
            'title' => 'Optimize images for web',
            'description' => 'Compress and optimize all images for faster loading',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => now()->addDays(7),
            'order' => 3,
        ]);

        // Project 2: Mobile App Development
        $project2 = Project::create([
            'tenant_id' => $acmeTenant->id,
            'user_id' => $admin->id,
            'name' => 'Mobile App Development',
            'description' => 'Native mobile app for iOS and Android',
            'status' => 'active',
            'start_date' => now()->subDays(20),
            'end_date' => now()->addDays(60),
        ]);

        Task::create([
            'tenant_id' => $acmeTenant->id,
            'project_id' => $project2->id,
            'user_id' => $admin->id,
            'title' => 'Set up development environment',
            'description' => 'Configure React Native and necessary tools',
            'status' => 'done',
            'priority' => 'urgent',
            'due_date' => now()->subDays(15),
            'order' => 1,
        ]);

        Task::create([
            'tenant_id' => $acmeTenant->id,
            'project_id' => $project2->id,
            'user_id' => $user->id,
            'title' => 'Implement user authentication',
            'description' => 'Build login and registration screens',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => now()->addDays(5),
            'order' => 2,
        ]);

        Task::create([
            'tenant_id' => $acmeTenant->id,
            'project_id' => $project2->id,
            'title' => 'Design app icon and splash screen',
            'description' => 'Create branding assets for the mobile app',
            'status' => 'todo',
            'priority' => 'low',
            'due_date' => now()->addDays(15),
            'order' => 3,
        ]);

        // Project 3: API Integration
        $project3 = Project::create([
            'tenant_id' => $acmeTenant->id,
            'user_id' => $owner->id,
            'name' => 'Third-Party API Integration',
            'description' => 'Integrate payment gateway and analytics APIs',
            'status' => 'on_hold',
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(20),
        ]);

        Task::create([
            'tenant_id' => $acmeTenant->id,
            'project_id' => $project3->id,
            'user_id' => $admin->id,
            'title' => 'Research payment providers',
            'description' => 'Compare Stripe, PayPal, and Square',
            'status' => 'done',
            'priority' => 'medium',
            'due_date' => now()->subDays(2),
            'order' => 1,
        ]);

        Task::create([
            'tenant_id' => $acmeTenant->id,
            'project_id' => $project3->id,
            'title' => 'Implement Stripe integration',
            'description' => 'Add Stripe SDK and create payment flow',
            'status' => 'todo',
            'priority' => 'high',
            'due_date' => now()->addDays(10),
            'order' => 2,
        ]);

        $this->command->info('Demo projects and tasks seeded successfully!');
    }
}
