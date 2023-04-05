<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
      'task_id', 'tag_id'
    ];

    public function tasks ()
    {
        return $this->belongsToMany(Task::class);
    }

}
