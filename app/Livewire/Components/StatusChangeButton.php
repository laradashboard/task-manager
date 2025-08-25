<?php

declare(strict_types=1);

namespace Modules\TaskManager\Livewire\Components;

use Livewire\Component;
use Modules\TaskManager\Models\Task;

class StatusChangeButton extends Component
{
    public Task $task;
    public $status;
    public $statuses;

    public function mount(Task $task)
    {
        $this->task = $task;
        $this->status = $task->status;
        $this->statuses = Task::statuses();
    }

    public function changeStatusTo($newStatus)
    {
        $this->status = $newStatus;
        $this->task->update(['status' => $newStatus]);
        $this->task->refresh();
        $this->dispatch('task-status-updated', $this->task->id);
    }

    public function render()
    {
        return view('taskmanager::livewire.components.status-change-button');
    }
}
