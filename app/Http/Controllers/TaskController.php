<?php

namespace App\Http\Controllers;

use App\Models\TaskModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return response()->json(['status' => true, 'message' => 'Task created successfully', 'task' => $task], 201);
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string|in:pending,in_progress,completed',
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
            $task->update([
                'title' => $request->title ?? $task->title,
                'description' => $request->description ?? $task->description,
                'due_date' => $request->due_date ?? $task->due_date,
                'status' => $request->status ?? $task->status,
            ]);
            return response()->json(['status' => true, 'message' => 'Task updated successfully', 'task' => $task]);
        } else {
            return response()->json(['status' => false, 'message' => 'Task not found', 'task' => $task]);
        }
    }
    public function destroy($id)
    {
        $task = TaskModel::find($id);
        if ($task) {
            $task->delete();
            return response()->json(['status' => true, 'message' => 'Task deleted successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Task not found']);
        }
    }
    public function assignUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|array',
            'user_id.*' => 'required|integer|exists:users,id,deleted_at,NULL',
            'task_id' => 'required|integer|exists:tasks,id,deleted_at,NULL',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }
        $task = TaskModel::find($request->task_id);
        $newUserIds = array_diff($request->user_id, $task->users->pluck('id')->toArray());
        if (!empty($newUserIds)) {
            $task->users()->attach($newUserIds);
        }
        return response()->json([
            'status' => true,
            'message' => 'Task Assigned Successfully',
        ]);
    }

    public function unassignUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|array',
            'user_id.*' => 'required|integer|exists:users,id,deleted_at,NULL',
            'task_id' => 'required|integer|exists:tasks,id,deleted_at,NULL',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ]);
        }
        $task = TaskModel::find($request->task_id);
        $assignedUserIds = $task->users->pluck('id')->toArray();
        $userIdsToDetach = array_intersect($assignedUserIds, $request->user_id);

        if (!empty($userIdsToDetach)) {
            $task->users()->detach($userIdsToDetach);
        }
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

    public function tasksAssignedToUser($userId)
    {
        $tasks = TaskModel::whereHas('users', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->get();

        if ($tasks->count() > 0)
            return response()->json(['status' => true, 'tasks' => $tasks, 'count' => $tasks->count()]);
        else
            return response()->json(['status' => false, 'tasks' => [], 'count' => 0]);
    }
    public function currentUserTasks()
    {
        $user = Auth::user();
        $tasks = $user->tasks()->get();

        if ($tasks->count() > 0)
            return response()->json(['status' => true, 'tasks' => $tasks, 'count' => $tasks->count()]);
        else
            return response()->json(['status' => false, 'tasks' => [], 'count' => 0]);
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
        if ($tasks->count() > 0)
            return response()->json(['status' => true, 'tasks' => $tasks, 'count' => $tasks->count()]);
        else
            return response()->json(['status' => false, 'tasks' => [], 'count' => 0]);
    }
}
