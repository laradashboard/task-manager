<?php

namespace Modules\TaskManager\Enums\Hooks;

enum TaskHook: string
{
    // Actions
    case DELETE_AFTER = 'task_delete_after';
    case CREATED = 'task_created';
    case UPDATED = 'task_updated';
    case COMPLETED = 'task_completed';
    case ASSIGNED = 'task_assigned';
    case STATUS_CHANGED = 'task_status_changed';

    // Filters
    case STATUS_OPTIONS = 'task_status_options';
    case PRIORITY_OPTIONS = 'task_priority_options';
    case ASSIGNABLE_USERS = 'task_assignable_users';
}
