<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use App\Repositories\ProjectRepository;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProjectService $projectService;
    protected Tenant $tenant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->projectService = new ProjectService(new ProjectRepository());

        $this->tenant = Tenant::create([
            'name' => 'Test Tenant',
            'slug' => 'test-tenant',
            'is_active' => true,
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Authenticate as this user
        auth()->login($this->user);
    }

    public function test_create_project_sets_authenticated_user_as_owner(): void
    {
        $data = [
            'name' => 'Test Project',
            'description' => 'Test Description',
            'status' => 'active',
        ];

        $project = $this->projectService->create($data);

        $this->assertEquals($this->user->id, $project->user_id);
        $this->assertEquals($this->tenant->id, $project->tenant_id);
    }

    public function test_find_by_id_returns_project_with_relations(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'Test Project',
            'status' => 'active',
        ]);

        $found = $this->projectService->findById($project->id);

        $this->assertNotNull($found);
        $this->assertEquals($project->id, $found->id);
        $this->assertTrue($found->relationLoaded('user'));
    }

    public function test_update_project_modifies_attributes(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'status' => 'active',
        ]);

        $updated = $this->projectService->update($project, [
            'name' => 'Updated Name',
            'status' => 'completed',
        ]);

        $this->assertEquals('Updated Name', $updated->name);
        $this->assertEquals('completed', $updated->status);
    }

    public function test_delete_project_soft_deletes(): void
    {
        $project = Project::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'name' => 'To Delete',
            'status' => 'active',
        ]);

        $result = $this->projectService->delete($project);

        $this->assertTrue($result);
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }
}
