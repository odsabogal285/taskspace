<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskList extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'default'
    ];

    protected $hidden = ['pivot', 'updated_at', 'deleted_at'];

    public function tasks ()
    {
        return $this->hasMany(Task::class);
    }

    public function users ()
    {
        return $this->belongsToMany(User::class, 'user_task_list')->withTimestamps();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $model = $this->where($field ?? $this->getRouteKeyName(), $value)->first();

        if (!$model) {
            abort(404, 'Lista de tareas inexistente.');
        }

        return $model;
    }
}
