<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use App\Repositories\TaskListRepository;
use App\Repositories\TaskRepository;
use Illuminate\Auth\Access\Response;

class TaskListPolicy
{
    public function all(User $user, TaskList $task, TaskListRepository $taskListRepository): Response
    {
        return $user->id === ($taskListRepository->getUser($task->id)?$taskListRepository->getUser($task->id)->users->first()->id:null)
            ? Response::allow()
            : Response::denyWithStatus(403);
    }
}
