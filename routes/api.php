<?php

use Illuminate\Support\Facades\Route;
use Modules\TaskManager\Http\Controllers\TaskManagerController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('taskmanagers', TaskManagerController::class)->names('taskmanager');
});
