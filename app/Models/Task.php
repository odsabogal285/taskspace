<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'task_list_id',
        'name',
        'description',
        'finished'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at'
    ];

    public function tags ()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function task_list ()
    {
        return $this->belongsTo(TaskList::class);
    }


    public function reminders ()
    {
        return $this->hasMany(Reminder::class);
    }
}
