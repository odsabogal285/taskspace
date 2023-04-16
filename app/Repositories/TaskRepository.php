<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TaskRepository extends BaseRepository
{
    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function getUser(int $id)
    {
        return $this->model->select(['tasks.*'])
                            ->join('task_lists', 'task_lists.id', '=', 'tasks.task_list_id')
                            ->join('user_task_list', 'user_task_list.task_list_id', '=', 'task_lists.id')
                            ->where('user_id', Auth::id())
                            ->where('tasks.id', $id)
                            ->first();
    }
}
