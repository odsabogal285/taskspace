<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\storeTask;
use App\Models\Task;
use App\Models\TaskList;
use App\Repositories\TaskRepository;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    private TaskRepository $taskRepository;
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function index (Request $request, TaskList $task_list)
    {
        try {

            return response()->success("Tareas de la lista de tareas: {$task_list->name}", $task_list->tasks);

        } catch (\Exception $exception) {
            Log::error("Error index TC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->error($exception->getMessage(), 500);
        }
    }

    public function show (Request $request, Task $task, TaskService $taskService)
    {
        try {

            if(!$taskService->validateTaskUser($task, $this->taskRepository)) {
                return response()->error('Tarea no valida.', 406);
            };

            return response()->success("Tarea: {$task->name}", $task);

        } catch (\Exception $exception) {
            Log::error("Error show TC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->error($exception->getMessage(), 500);
        }

    }
    public function store (storeTask $request): JsonResponse
    {
        try {

            DB::beginTransaction();

            $task = new Task($request->all());
            $task = $this->taskRepository->save($task);

            DB::commit();

            return response()->success('Task created', array('task' =>  $task), 201);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Error store TC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->error($exception->getMessage(), 500);
        }
    }

    public function update (Request $request, Task $task, TaskService $taskService): JsonResponse
    {
        try {

            DB::beginTransaction();

            if(!$taskService->validateTaskUser($task, $this->taskRepository)) {
                return response()->error('Tarea no valida.', 406);
            };

            $task->fill($request->all());

            $task = $this->taskRepository->save($task);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tarea actualizada correctamente',
                'data' => $task
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Error update TC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->error($exception->getMessage(), 500);
        }

    }

    public function destroy (Request $request, Task $task, TaskService $taskService): JsonResponse
    {
        try {
            DB::beginTransaction();

            if(!$taskService->validateTaskUser($task, $this->taskRepository)) {
                return response()->error('Tarea no valida.', 406);
            };

            $this->taskRepository->delete($task);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Tarea eliminada correctamente',
                'data' => $task
            ]);

        } catch (\Exception $exception){
            DB::rollBack();
            Log::error("Error destroy TC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->error($exception->getMessage(), 500);
        }
    }
}
