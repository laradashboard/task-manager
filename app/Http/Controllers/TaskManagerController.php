<?php

declare(strict_types=1);

namespace Modules\TaskManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\TaskManager\Models\Task;
use Modules\TaskManager\Services\TaskService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Modules\TaskManager\Http\Requests\TaskRequest;
use App\Enums\ActionType;

class TaskManagerController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {
    }

    public function index()
    {
        $this->authorize('viewAny', Task::class);

        $this->setBreadcrumbTitle(__('Tasks'));

        return $this->renderViewWithBreadcrumbs('taskmanager::index');
    }

    public function create()
    {
        $this->authorize('create', Task::class);

        $this->setBreadcrumbTitle(__('Create Task'))
            ->addBreadcrumbItem(__('Tasks'), route('admin.tasks.index'));

        return $this->renderViewWithBreadcrumbs('taskmanager::create', [
            'statuses' => Task::statuses(),
            'priorities' => Task::priorities(),
            'users' => User::pluck('first_name', 'id')->toArray(),
        ]);
    }

    public function store(TaskRequest $request)
    {
        $this->authorize('create', Task::class);

        try {
            $this->taskService->createTask($request->validated());
            $this->storeActionLog(ActionType::CREATED, ['task' => $request->validated()]);
            return redirect()->route('admin.tasks.index')->with('success', __('Task created successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Failed to create task.'));
        }
    }

    public function edit(int $id)
    {
        $task = $this->taskService->getTaskById((int) $id);
        $this->authorize('update', $task);

        $this->setBreadcrumbTitle(__('Edit Task'))
            ->addBreadcrumbItem(__('Tasks'), route('admin.tasks.index'));

        return $this->renderViewWithBreadcrumbs('taskmanager::edit', [
            'task' => $task,
            'statuses' => Task::statuses(),
            'priorities' => Task::priorities(),
            'users' => User::pluck('first_name', 'id')->toArray(),
        ]);
    }

    public function show(int $id)
    {
        $task = $this->taskService->getTaskById((int) $id);
        $this->authorize('view', $task);

        $this->setBreadcrumbTitle(__('View Task'))
            ->addBreadcrumbItem(__('Tasks'), route('admin.tasks.index'));

        return $this->renderViewWithBreadcrumbs('taskmanager::show', [
            'task' => $task,
            'statuses' => Task::statuses(),
            'priorities' => Task::priorities(),
            'users' => User::pluck('first_name', 'id')->toArray(),
        ]);
    }

    public function update(TaskRequest $request, int $id): RedirectResponse
    {
        $task = $this->taskService->getTaskById((int) $id);
        $this->authorize('update', $task);

        try {
            $this->taskService->updateTask($task, $request->validated());

            return redirect()->route('admin.tasks.index')->with('success', __('Task updated successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Failed to update task.'));
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $task = $this->taskService->getTaskById((int) $id);
        $this->authorize('delete', $task);

        try {
            $this->taskService->deleteTask($task);
            $this->storeActionLog(ActionType::DELETED, ['task' => $task->toArray()]);
            return redirect()->route('admin.tasks.index')->with('success', __('Task deleted successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Failed to delete task.'));
        }
    }
}
