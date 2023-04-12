<?php

namespace App\Repositories;

use App\Models\TaskList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class TaskListRepository extends BaseRepository
{

    public function __construct(TaskList $taskList)
    {
        parent::__construct($taskList);
    }

    public function saveDefault (Model $model)
    {
        $model->default = true;
        $model->save();
        return $model;
    }
    public function syncUsers(TaskList $taskList, $users): array
    {
       return $taskList->users()->sync(Arr::pluck($users, 'id'));
    }
}
