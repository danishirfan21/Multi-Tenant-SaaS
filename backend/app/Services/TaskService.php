<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct(
        private TaskRepository $taskRepository
    ) {}

    public function getAll(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->taskRepository->paginate($perPage, $filters);
    }

    public function getByProject(int $projectId, array $filters = []): Collection
    {
        return $this->taskRepository->getByProject($projectId, $filters);
    }

    public function findById(int $id): ?Task
    {
        return $this->taskRepository->find($id);
    }

    public function create(array $data): Task
    {
        // Auto-set order to end of list if not specified
        if (!isset($data['order'])) {
            $data['order'] = $this->taskRepository->getMaxOrder($data['project_id']) + 1;
        }

        return $this->taskRepository->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $this->taskRepository->update($task, $data);

        return $task->fresh();
    }

    public function delete(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }

    public function updateStatus(Task $task, string $status): Task
    {
        return $this->update($task, ['status' => $status]);
    }

    public function assignToUser(Task $task, ?int $userId): Task
    {
        return $this->update($task, ['user_id' => $userId]);
    }
}
