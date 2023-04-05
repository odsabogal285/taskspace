<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\storeTask;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    private TaskRepository $taskRepository;
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function store (storeTask $request): JsonResponse
    {
        try {

            $task = new Task($request->all());
            $task = $this->taskRepository->save($task);

            return response()->json([
                'status' => 'success',
                'message' => 'Task created',
                'data' => [
                    'task' =>  $task
                ]
            ]);

        } catch (\Exception $exception) {
            Log::error("Error store TC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => null
            ]);
        }
    }
}
