<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant1;
    protected Tenant $tenant2;
    protected User $user1;
    protected User $user2;
    protected Project $project1;
    protected Project $project2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create two separate tenants
        $this->tenant1 = Tenant::create([
            'name' => 'Tenant One',
            'slug' => 'tenant-one',
            'is_active' => true,
        ]);

        $this->tenant2 = Tenant::create([
            'name' => 'Tenant Two',
            'slug' => 'tenant-two',
            'is_active' => true,
        ]);

        // Create users for each tenant
        $this->user1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'User One',
            'email' => 'user1@tenant1.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->user2 = User::create([
            'tenant_id' => $this->tenant2->id,
            'name' => 'User Two',
            'email' => 'user2@tenant2.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create projects for each tenant
        $this->project1 = Project::create([
            'tenant_id' => $this->tenant1->id,
            'user_id' => $this->user1->id,
            'name' => 'Project One',
            'status' => 'active',
        ]);

        $this->project2 = Project::create([
            'tenant_id' => $this->tenant2->id,
            'user_id' => $this->user2->id,
            'name' => 'Project Two',
            'status' => 'active',
        ]);
    }

    public function test_user_can_only_see_their_tenant_projects(): void
    {
        $token = auth()->login($this->user1);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/projects');

        $response->assertStatus(200);

        $projects = $response->json('data');

        $this->assertCount(1, $projects);
        $this->assertEquals($this->project1->id, $projects[0]['id']);
    }

    public function test_user_cannot_access_another_tenant_project(): void
    {
        $token = auth()->login($this->user1);

        // Try to access project from tenant2
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/projects/{$this->project2->id}");

        $response->assertStatus(404);
    }

    public function test_user_cannot_create_project_for_another_tenant(): void
    {
        $token = auth()->login($this->user1);

        // Try to create project with explicit tenant_id for tenant2
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/projects', [
            'tenant_id' => $this->tenant2->id,
            'name' => 'Malicious Project',
            'status' => 'active',
        ]);

        // Project should be created but automatically scoped to user1's tenant
        $response->assertStatus(201);

        $project = Project::find($response->json('data.id'));
        $this->assertEquals($this->tenant1->id, $project->tenant_id);
    }
}
