<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskListController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [LoginController::class, 'registerUser']);

    // Route::get('/user-profile', [\App\Http\Controllers\Api\LoginController::class, 'profile']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/user-profile', [UserController::class, 'profile']);

        //Task
        Route::get('/task/all/{task_list}', [TaskController::class, 'index']);
        Route::get('/task/{task}', [TaskController::class, 'show']);
        Route::post('/task', [TaskController::class, 'store']);
        Route::put('/task/{task}', [TaskController::class, 'update']);
        Route::delete('/task/{task}', [TaskController::class, 'destroy']);

        //Task list
        Route::get('/task-list', [TaskListController::class, 'index']);
        Route::get('/task-list/{task_list}', [TaskListController::class, 'show']);
        Route::post('/task-list', [TaskListController::class, 'store']);
        Route::put('/task-list/{task_list}', [TaskListController::class, 'update']);
        Route::delete('/task-list/{task_list}', [TaskListController::class, 'destroy']);

    });
});

