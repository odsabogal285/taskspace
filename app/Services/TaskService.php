<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;

class TaskService
{

    public function validateTaskUser (Task $task, TaskRepository $taskRepository): bool
    {
        return (boolean) $taskRepository->getUser($task->id);
    }
}
