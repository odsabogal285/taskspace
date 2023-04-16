<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function all(User $user, Task $task, TaskRepository $taskRepository): Response
    {
        return $user->id === ($taskRepository->getUserId($task->id)?$taskRepository->getUserId($task->id)->user_id:null)
            ? Response::allow()
            : Response::denyWithStatus(403);
    }
}
