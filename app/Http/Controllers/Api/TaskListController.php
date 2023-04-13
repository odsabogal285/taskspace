<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\task\storeTaskList;
use App\Http\Requests\Api\task\updateTaskList;
use App\Models\TaskList;
use App\Repositories\TaskListRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TaskListController extends Controller
{
    private TaskListRepository $taskListRepository;
    public function __construct(TaskListRepository $taskListRepository)
    {
        $this->taskListRepository = $taskListRepository;
    }

    public function store (storeTaskList $request): JsonResponse
    {
        try {

            DB::beginTransaction();

            $task_list = new TaskList($request->all());
            $task_list = $this->taskListRepository->save($task_list);

            $task_list = $this->taskListRepository->syncUsers($task_list, array(['id' => Auth::user()->id]));

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Lista de tareas creada correctamente.',
                'data' => $task_list
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Error store TLC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->json([
                'status' => 'error',
                'message' => "Error fatal: {$exception->getMessage()}",
                'data' => null
            ], 500);
        }
    }
    public function update (updateTaskList $request, TaskList $task_list): JsonResponse
    {
        try {

            DB::beginTransaction();

            $task_list->fill($request->all());

            $task_list = $this->taskListRepository->save($task_list);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Lista de tareas actualizada.',
                'data' => $task_list
            ]);

        }catch (\Exception $exception){
            DB::rollBack();
            Log::error("Error update TLC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->json([
                'status' => 'error',
                'message' => "Error fatal: {$exception->getMessage()}",
                'data' => null
            ], 500);
        }
    }

    public function destroy (Request $request, TaskList $task_list): JsonResponse
    {
        try {

            DB::beginTransaction();

            $this->taskListRepository->delete($task_list);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Lista de tareas eliminada',
                'data' => $task_list
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Error store TLC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->json([
                'status' => 'error',
                'message' => "Error fatal: {$exception->getMessage()}",
                'data' => null
            ], 500);
        }
    }
}
