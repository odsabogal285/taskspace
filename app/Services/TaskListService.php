<?php

namespace App\Services;

use App\Models\TaskList;
use App\Models\User;
use App\Repositories\TaskListRepository;

class TaskListService
{
    private TaskList $task_list;
    private TaskListRepository $taskListRepository;

    public function __construct(TaskList $task_list, TaskListRepository $taskListRepository)
    {
        $this->task_list = $task_list;
        $this->taskListRepository = $taskListRepository;
    }

    public function validateTaskListUser (TaskList $task_list, TaskListRepository $taskListRepository): bool
    {
        return (boolean) $taskListRepository->get($task_list->id);
    }

    public function validateTaskListUserDefault (TaskList $task_list, TaskListRepository $taskListRepository): bool
    {
        return (boolean) $taskListRepository->getWithoutDefault($task_list->id);
    }
}
