<livewire:taskmanager::components.status-change-button
    :task="$task"
    :status="$task->status"
    :statuses="$statuses"
    :key="'status-change-' . $task->id"
/>