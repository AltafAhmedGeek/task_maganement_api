<?php

namespace App\Http\Controllers;

use App\Models\TaskModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'status' => 'required|string|in:pending,in progress,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }


        $task = TaskModel::create($validator->validated());

        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'status' => 'required|string|in:pending,in progress,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }

        $task = TaskModel::find($id);
        if ($task) {
            $task->update($validator->validated());
            return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
        } else {
            return response()->json(['message' => 'Task not found', 'task' => $task]);
        }
    }
    public function destroy($id)
    {
        $task = TaskModel::find($id);
        if ($task) {
            $task->delete();
            return response()->json(['message' => 'Task deleted successfully']);
        } else {
            return response()->json(['message' => 'Task not found']);
        }
    }
    public function assignUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id,deleted_at,NULL',
            'task_id' => 'required|exists:tasks,id,deleted_at,NULL',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }
        $task = TaskModel::find($request->task_id);
        $alreadyAssigned = DB::table('task_user')
            ->where('user_id', $request->user_id)
            ->where('task_id', $request->task_id)
            ->exists();
        if ($alreadyAssigned) {
            return response()->json([
                'status' => false,
                'message' => 'Task Already assigned to User.',
            ]);
        }
        $task->users()->attach($request->user_id);

        return response()->json([
            'status' => true,
            'message' => 'Task Assigned Successfully',
        ]);
    }

    public function unassignUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id,deleted_at,NULL',
            'task_id' => 'required|exists:tasks,id,deleted_at,NULL',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }
        $alreadyAssigned = DB::table('task_user')
            ->where('user_id', $request->user_id)
            ->where('task_id', $request->task_id)
            ->exists();
        if (!$alreadyAssigned) {
            return response()->json([
                'status' => false,
                'message' => 'Task Not assigned to User.',
            ]);
        }
        $task = TaskModel::find($request->task_id);
        $task->users()->detach($request->user_id);

        return response()->json([
            'status' => true,
            'message' => 'Task Unassigned Successfully',
        ]);
    }

    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id,deleted_at,NULL',
            'status' => 'required|in:pending,in_progress,completed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }
        $task = TaskModel::find($request->task_id);
        if ($task->status !== $request->status) {
            $task->update(['status' => $request->status]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Task Updated Successfully',
        ]);
    }

    public function userTasks(Request $request)
    {
        $user = Auth::user();

        $tasks = $user->tasks()->get();

        return response()->json($tasks);
    }
    public function index(Request $request)
    {
        $query = TaskModel::select('id', 'title', 'description', 'due_date', 'status');

        if ($request->has('status')) {
            $query->where('status', 'like', '%' . $request->status . '%');
        }

        if ($request->has('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        if ($request->has('user_id')) {
            $query->whereHas('users', function ($subQuery) use ($request) {
                $subQuery->where('id', $request->user_id);
            });
        }

        $tasks = $query->get();

        return response()->json(['tasks' => $tasks]);
    }
}
