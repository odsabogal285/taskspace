<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\task\storeTaskList;
use App\Http\Requests\Api\task\updateTaskList;
use App\Models\TaskList;
use App\Repositories\TaskListRepository;
use App\Services\TaskListService;
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

    public function index (Request $request): JsonResponse
    {
        try {

            return response()->success('Lista de tareas',  Auth::user()->task_lists);

        } catch (\Exception $exception) {
            Log::error("Error index TLC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->error($exception->getMessage(), 500);
        }

    }

    public function show (Request $request, TaskList $task_list, TaskListService $taskListService): JsonResponse
    {
        try {

            if (!$taskListService->validateTaskListUser($task_list, $this->taskListRepository)) {
                return response()->error('Lista de tarea no valida.', 406);
            }

            return response()->success('Lista de tareas encontrada con exito.', $task_list);

        } catch (\Exception $exception) {
            Log::error("Error show TLC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->error($exception->getMessage(), 500);
        }
    }

    public function store (storeTaskList $request): JsonResponse
    {
        try {

            DB::beginTransaction();

            $task_list = new TaskList($request->all());
            $task_list = $this->taskListRepository->save($task_list);

            $task_list = $this->taskListRepository->syncUsers($task_list, array(['id' => Auth::user()->id]));

            DB::commit();

            return response()->success('Lista de tareas creada correctamente.', $task_list);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Error store TLC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->error($exception->getMessage(), 500);
        }
    }
    public function update (updateTaskList $request, TaskList $task_list, TaskListService $taskListService): JsonResponse
    {
        try {

            DB::beginTransaction();

            if (!$taskListService->validateTaskListUser($task_list, $this->taskListRepository)) {
                return response()->error('Lista de tarea no valida.', 406);
            }

            $task_list->fill($request->all());

            $task_list = $this->taskListRepository->save($task_list);

            DB::commit();

            return response()->success('Lista de tareas actualizada', $task_list, 201);

        }catch (\Exception $exception){
            DB::rollBack();
            Log::error("Error update TLC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->error($exception->getMessage(), 500);
        }
    }

    public function destroy (Request $request, TaskList $task_list, TaskListService $taskListService): JsonResponse
    {
        try {

            DB::beginTransaction();

            if (!$taskListService->validateTaskListUserDefault($task_list, $this->taskListRepository)) {
                return response()->error('Lista de tarea no valida.', 406);
            }

            $this->taskListRepository->delete($task_list);

            DB::commit();

            return response()->success('Lista de tareas eliminada', $task_list);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Error store TLC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->error($exception->getMessage(), 500);
        }
    }
}
