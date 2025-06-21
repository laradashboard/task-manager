<?php

declare(strict_types=1);

namespace Modules\TaskManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkAuthorization(Auth::user(), ['task.view']);

        $filters = [
            'search' => request('search'),
            'status' => request('status'),
            'priority' => request('priority'),
        ];

        return view('taskmanager::index', [
            'tasks' => $this->taskService->getTasks($filters),
            'filters' => $filters,
            'statuses' => Task::statuses(),
            'priorities' => Task::priorities(),
            'breadcrumbs' => [
                'title' => __('Tasks'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkAuthorization(Auth::user(), ['task.create']);

        return view('taskmanager::create', [
            'statuses' => Task::statuses(),
            'priorities' => Task::priorities(),
            'users' => User::pluck('name', 'id')->toArray(),
            'breadcrumbs' => [
                'title' => __('Create Task'),
                'items' => [
                    [
                        'label' => __('Tasks'),
                        'url' => route('admin.tasks.index'),
                    ],
                ],
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $this->checkAuthorization(Auth::user(), ['task.create']);

        try {
            $this->taskService->createTask($request->validated());
            $this->storeActionLog(ActionType::CREATED, ['task' => $request->validated()]);
            return redirect()->route('admin.tasks.index')->with('success', __('Task created successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Failed to create task.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->checkAuthorization(Auth::user(), ['task.edit']);

        $task = $this->taskService->getTaskById((int) $id);
        return view('taskmanager::edit', [
            'task' => $task,
            'statuses' => Task::statuses(),
            'priorities' => Task::priorities(),
            'users' => User::pluck('name', 'id')->toArray(),
            'breadcrumbs' => [
                'title' => __('Edit Task'),
                'items' => [
                    [
                        'label' => __('Tasks'),
                        'url' => route('admin.tasks.index'),
                    ],
                ],
            ],
        ]);
    }

    public function update(TaskRequest $request, $id): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['task.edit']);

        try {
            $task = $this->taskService->getTaskById($id);
            $this->taskService->updateTask($task, $request->validated());
            $this->storeActionLog(ActionType::UPDATED, ['task' => $task]);
            return redirect()->route('admin.tasks.index')->with('success', __('Task updated successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Failed to update task.'));
        }
    }

    public function destroy($id)
    {
        $this->checkAuthorization(Auth::user(), ['task.delete']);

        try {
            $task = $this->taskService->getTaskById($id);
            $this->taskService->deleteTask($task);
            $this->storeActionLog(ActionType::DELETED, ['task' => $task]);
            return redirect()->route('admin.tasks.index')->with('success', __('Task deleted successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Failed to delete task.'));
        }
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $this->checkAuthorization(Auth::user(), ['task.delete']);

        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.tasks.index')
                ->with('error', __('No tasks selected for deletion'));
        }

        $tasks = $this->taskService->getTasksByIds($ids);
        $deletedCount = 0;

        foreach ($tasks as $task) {
            $task = ld_apply_filters('task_delete_before', $task);
            $task->delete();
            ld_apply_filters('task_delete_after', $task);

            $this->storeActionLog(ActionType::DELETED, ['task' => $task]);
            ld_do_action('task_delete_after', $task);

            $deletedCount++;
        }

        if ($deletedCount > 0) {
            session()->flash('success', __(':count tasks deleted successfully', ['count' => $deletedCount]));
        } else {
            session()->flash('error', __('No tasks were deleted. Selected tasks may include protected accounts.'));
        }

        return redirect()->route('admin.tasks.index');
    }
}
