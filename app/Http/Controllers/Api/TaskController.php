<?php
.....
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index()
    {
        $tasks = $this->taskService->all();
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = $this->taskService->add($validated);
        return response()->json($task, 201);
    }

    public function update(Request $request, string $id)
    {
        $task = $this->taskService->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $validated = $request->validate([
            'completed' => 'required|boolean',
        ]);

        $updatedTask = $this->taskService->update($id, $validated);
        return response()->json($updatedTask);
    }

    public function destroy(string $id)
    {
        $task = $this->taskService->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $this->taskService->delete($id);
        return response()->json(null, 204);
    }
}