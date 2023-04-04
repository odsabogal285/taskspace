<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all ()
    {
        return $this->model->get();
    }
    public function get (int $id)
    {
        return $this->model->find($id);
    }

    public function save (Model $model)
    {
        $model->save();
        return $model;
    }

    public function delete (Model $model)
    {
        $model->delete();
        return $model;
    }
}
