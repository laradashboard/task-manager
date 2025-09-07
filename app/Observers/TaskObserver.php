<?php

declare(strict_types=1);

namespace Modules\TaskManager\Observers;

use App\Concerns\HasActionLogTrait;
use App\Enums\ActionType;
use Modules\TaskManager\Models\Task;

class TaskObserver
{
    use HasActionLogTrait;

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $this->storeActionLog(ActionType::CREATED, ['task' => $task]);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $this->storeActionLog(ActionType::UPDATED, ['task' => $task]);
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        $this->storeActionLog(ActionType::DELETED, ['task' => $task]);
    }
}
