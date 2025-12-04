<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTaskRequest;
use App\Http\Requests\Api\V1\UpdateTaskRequest;
use App\Http\Resources\V1\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $filters = $request->only(['project_id', 'status', 'priority', 'search', 'sort_by', 'sort_order']);

        $tasks = $this->taskService->getAll($perPage, $filters);

        return response()->json([
            'data' => TaskResource::collection($tasks),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
        ]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request->validated());

        return response()->json([
            'message' => 'Task created successfully',
            'data' => new TaskResource($task->load(['project', 'user'])),
        ], 201);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'data' => new TaskResource($task->load(['project', 'user'])),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task = $this->taskService->update($task, $request->validated());

        return response()->json([
            'message' => 'Task updated successfully',
            'data' => new TaskResource($task->load(['project', 'user'])),
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->delete($task);

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }
}
