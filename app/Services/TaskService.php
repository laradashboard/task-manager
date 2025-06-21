<?php

declare(strict_types=1);

namespace Modules\TaskManager\Services;

use Modules\TaskManager\Models\Task;

class TaskService
{
    /**
     * Get tasks with filters
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getTasks(array $filters = [])
    {
        $query = Task::applyFilters($filters);

        if (isset($filters['priority']) && $filters['priority']) {
            $query->where('priority', $filters['priority']);
        }

        return $query->paginateData();
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task
    {
        $task = new Task();
        $task->fill($data);
        $task->created_by = auth()->id();
        $task->save();

        return $task;
    }

    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function updateTask(Task $task, array $data): Task
    {
        $task->fill($data);
        $task->save();

        return $task;
    }

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return void
     */
    public function deleteTask(Task $task): void
    {
        $task->delete();
    }

    /**
     * Get task by ID.
     *
     * @param int $id
     * @return Task|null
     */
    public function getTaskById(int $id): ?Task
    {
        return Task::find($id);
    }

    /**
     * Get tasks by task ids.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTasksByIds(array $taskIds)
    {
        return Task::whereIn('id', $taskIds)->get();
    }
}
