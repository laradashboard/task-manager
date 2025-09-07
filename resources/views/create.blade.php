<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters('tasks.after_breadcrumbs', '') !!}

    <x-card>
        @include('taskmanager::partials.form', [
            'action' => route('admin.tasks.store'),
            'method' => 'POST',
            'task' => null,
        ])
    </x-card>

    @push('scripts')
    <x-quill-editor :editor-id="'description'" />
    @endpush
</x-layouts.backend-layout>