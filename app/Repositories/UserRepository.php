<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository
{

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function get (int $id)
    {
        return $this->model->with('task_lists')->find($id);
    }

    public function save(Model $model)
    {
        $model->password = bcrypt($model->password);
        $model->save();
        return $model;
    }


}
