@extends('backend.layouts.app')

@section('title')
    {{ $breadcrumbs['title'] }} | {{ config('app.name') }}
@endsection

@section('admin-content')

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
   <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    {!! ld_apply_filters('tasks_after_breadcrumbs', '') !!}

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                @include('taskmanager::partials.form', [
                    'action' => route('admin.tasks.update', $task->id),
                    'method' => 'PUT',
                    'task' => $task
                ])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<x-quill-editor :type="'basic'" :editor-id="'description'" />
@endpush