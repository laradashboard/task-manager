<?php

declare(strict_types=1);

namespace Modules\TaskManager\Livewire\Components;

use App\Livewire\Datatable\Datatable;
use Modules\TaskManager\Models\Task;

class TaskDatatable extends Datatable
{
    public string $status = '';
    public string $priority = '';
    public string $assigned_to = '';

    public string $model = Task::class;

    public array $queryString = [
        ...parent::QUERY_STRING_DEFAULTS,
        'status' => [],
        'priority' => [],
        'assigned_to' => [],
    ];

    protected function getSearchbarPlaceholder(): string
    {
        return __('Search by title or description...');
    }

    protected function getHeaders(): array
    {
        return [
            [
                'id' => 'title',
                'title' => __('Title'),
                'width' => null,
                'sortable' => true,
                'sortBy' => 'title',
            ],
            [
                'id' => 'status',
                'title' => __('Status'),
                'width' => '150px',
                'sortable' => true,
                'sortBy' => 'status',
            ],
            [
                'id' => 'priority',
                'title' => __('Priority'),
                'width' => '150px',
                'sortable' => true,
                'sortBy' => 'priority',
            ],
            [
                'id' => 'assigned_to',
                'title' => __('Assigned To'),
                'width' => null,
                'sortable' => true,
                'sortBy' => 'assigned_to',
            ],
            [
                'id' => 'created_at',
                'title' => __('Created At'),
                'width' => null,
                'sortable' => true,
                'sortBy' => 'created_at',
            ],
            [
                'id' => 'actions',
                'title' => __('Actions'),
                'sortable' => false,
                'is_action' => true,
            ],
        ];
    }

    public function getFilters(): array
    {
        return [
            [
                'id' => 'status',
                'label' => __('Status'),
                'filterLabel' => __('Status'),
                'icon' => 'lucide:filter',
                'allLabel' => __('All Statuses'),
                'options' => Task::statuses(),
                'selected' => $this->status,
            ],
            [
                'id' => 'priority',
                'label' => __('Priority'),
                'filterLabel' => __('Priority'),
                'icon' => 'lucide:flag',
                'allLabel' => __('All Priorities'),
                'options' => Task::priorities(),
                'selected' => $this->priority,
            ],
            [
                'id' => 'assigned_to',
                'label' => __('Assigned To'),
                'filterLabel' => __('Assigned To'),
                'icon' => 'lucide:user',
                'allLabel' => __('All Users'),
                'options' => $this->getUsersOptions(),
                'selected' => $this->assigned_to,
            ],
        ];
    }

    protected function buildQuery(): \Spatie\QueryBuilder\QueryBuilder
    {
        $query = parent::buildQuery()
            ->with('assigned')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->priority, function ($query) {
                $query->where('priority', $this->priority);
            })
            ->when($this->assigned_to, function ($query) {
                $query->where('assigned_to', $this->assigned_to);
            });

        return $this->sortQuery($query);
    }

    public function getUsersOptions(): array
    {
        return \App\Models\User::pluck('first_name', 'id')->toArray();
    }

    public function renderAssignedToColumn(Task $task): string
    {
        return $task->assigned ? $task->assigned->full_name : __('Unassigned');
    }

    public function renderTitleColumn(Task $task): string
    {
        return "<a href='" . route('admin.tasks.edit', $task->id) . "' class='flex items-center hover:text-primary'>
                <div class='flex flex-col'>
                    <span>" . $task->title . "</span>
                    <span class='text-xs text-gray-500 dark:text-gray-400'>" . $task->username . "</span>
                </div>
            </a>";
    }

    public function renderStatusColumn(Task $task): string
    {
        return view('taskmanager::partials.status-changer', [
            'task' => $task,
            'status' => $task->status,
            'statuses' => Task::statuses(),
        ])->render();
    }

    public function renderPriorityColumn(Task $task): string
    {
        $priorityClasses = match ($task->priority) {
            'low' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'high' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };

        return "<span class='badge " . $priorityClasses . "'>" . ($task->priority ? Task::priorities()[$task->priority] : __('N/A')) . "</span>";
    }
}
