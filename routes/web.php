<?php

use Illuminate\Support\Facades\Route;
use Modules\TaskManager\Http\Controllers\TaskManagerController;

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('tasks', TaskManagerController::class)
            ->names('tasks');
        Route::delete('tasks/delete/bulk-delete', [TaskManagerController::class, 'bulkDelete'])->name('tasks.bulk-delete');
    });
