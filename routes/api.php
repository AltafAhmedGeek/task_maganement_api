<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes without any middleware
Route::post('/login', [UserController::class, 'login'])->name('api.login');
Route::middleware(['api'])->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    Route::post('/tasks/assign', [TaskController::class, 'assignUser']);
    Route::post('/tasks/unassign', [TaskController::class, 'unassignUser']);
    Route::post('/tasks/changeStatus', [TaskController::class, 'changeStatus']);
    Route::get('/tasks/currentUserTasks', [TaskController::class, 'currentUserTasks']);
    Route::get('/tasks/tasksAssignedToUser/{id}', [TaskController::class, 'tasksAssignedToUser']);
});
