<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tasks';
    protected $fillable = ['title', 'description', 'due_date', 'status'];

    public function users()
    {
        return $this->belongsToMany(User::class,'task_user','task_id', 'user_id');
    }
}
