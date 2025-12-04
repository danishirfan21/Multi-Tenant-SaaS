<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Create Acme Corp tenant
        $acmeTenant = Tenant::create([
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
            'domain' => 'acme.example.com',
            'is_active' => true,
        ]);

        // Create owner for Acme
        User::create([
            'tenant_id' => $acmeTenant->id,
            'name' => 'John Doe',
            'email' => 'john@acme.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'is_active' => true,
        ]);

        // Create admin for Acme
        User::create([
            'tenant_id' => $acmeTenant->id,
            'name' => 'Jane Smith',
            'email' => 'jane@acme.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create regular user for Acme
        User::create([
            'tenant_id' => $acmeTenant->id,
            'name' => 'Bob Johnson',
            'email' => 'bob@acme.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'is_active' => true,
        ]);

        // Create TechStart tenant
        $techStartTenant = Tenant::create([
            'name' => 'TechStart Inc',
            'slug' => 'techstart',
            'domain' => 'techstart.example.com',
            'is_active' => true,
        ]);

        // Create owner for TechStart
        User::create([
            'tenant_id' => $techStartTenant->id,
            'name' => 'Alice Williams',
            'email' => 'alice@techstart.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'is_active' => true,
        ]);

        $this->command->info('Tenants and users seeded successfully!');
    }
}
