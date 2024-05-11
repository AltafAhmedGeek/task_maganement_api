<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TaskModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tasks';
    protected $fillable = ['title', 'description', 'due_date', 'status'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function getStatusAttribute($value)
    {
        return Str::title(str_replace('_', ' ', $value));
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user', 'task_id', 'user_id');
    }
}
