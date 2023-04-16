<?php

namespace App\Repositories;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class TaskListRepository extends BaseRepository
{

    public function __construct(TaskList $taskList)
    {
        parent::__construct($taskList);
    }

    // Lista de tareas
    public function get(int $id)
    {
        return $this->model->whereRelation('users', 'id', Auth::id())->find($id);
    }

    public function getUser(int $id)
    {
        return $this->model->select('id')
            ->whereRelation('users', 'id', Auth::id())
            ->with(['users' => function($query) {
                $query->select('id');
            }])->find($id);
    }

    // Lista de tareas que son son por defecto
    public function getWithoutDefault(int $id)
    {
        return $this->model->whereRelation('users', 'id', Auth::id())->where('default', false)->find($id);
    }
    public function saveDefault (Model $model)
    {
        $model->default = true;
        $model->save();
        return $model;
    }
    public function syncUsers(TaskList $taskList, $users)
    {
       $taskList->users()->sync(Arr::pluck($users, 'id'));
       return $this->get($taskList->id);
    }
}
