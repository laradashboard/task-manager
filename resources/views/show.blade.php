<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters('tasks.after_breadcrumbs', '') !!}

    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ $task->title }}
            </h3>
        </x-slot>

        <div class="prose max-w-none dark:prose-invert">
            {!! $task->description !!}

            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Created at') }}: {{ $task->created_at->format('d M, Y h:i A') }}
            </p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Last updated at') }}: {{ $task->updated_at->format('d M, Y h:i A') }}
            </p>

            <p class="mt-1 text-sm">
                <span
                    class="badge">
                    {{ ucfirst($task->status) }}
                </span>
            </p>

            <p class="mt-1 text-sm">
                <span
                    class="badge">
                    {{ ucfirst($task->priority) }}
                </span>
            </p>
        </div>
    </x-card>
</x-layouts.backend-layout>
