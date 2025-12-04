<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectService
{
    public function __construct(
        private ProjectRepository $projectRepository
    ) {}

    public function getAll(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->projectRepository->paginate($perPage, $filters);
    }

    public function findById(int $id): ?Project
    {
        return $this->projectRepository->find($id);
    }

    public function create(array $data): Project
    {
        // Set authenticated user as project owner if not specified
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        return $this->projectRepository->create($data);
    }

    public function update(Project $project, array $data): Project
    {
        $this->projectRepository->update($project, $data);

        return $project->fresh();
    }

    public function delete(Project $project): bool
    {
        return $this->projectRepository->delete($project);
    }

    public function getStats(int $tenantId): object
    {
        return $this->projectRepository->getStatsByTenant($tenantId);
    }
}
