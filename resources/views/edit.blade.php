<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters('tasks.after_breadcrumbs', '') !!}

    <x-card>
        @include('taskmanager::partials.form', [
            'action' => route('admin.tasks.update', $task->id),
            'method' => 'PUT',
            'task' => $task
        ])
    </x-card>

    @push('scripts')
    <x-quill-editor :editor-id="'description'" />
    @endpush
</x-layouts.backend-layout>