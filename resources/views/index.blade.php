@extends('backend.layouts.app')

@section('title')
   {{ $breadcrumbs['title'] }} | {{ config('app.name') }}
@endsection

@section('admin-content')

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6" x-data="{ selectedTasks: [], selectAll: false, bulkDeleteModalOpen: false }">
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    {!! ld_apply_filters('tasks_after_breadcrumbs', '') !!}

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="px-5 py-4 sm:px-6 sm:py-5 flex gap-3 md:gap-1 flex-col md:flex-row justify-between items-center">
                @include('backend.partials.search-form', [
                    'placeholder' => __('Search by title'),
                ])

                <div class="flex items-center justify-end w-full md:w-auto gap-4">
                    <div class="flex items-center gap-2">
                        <!-- Bulk Actions dropdown -->
                        <div class="flex items-center justify-center" x-show="selectedTasks.length > 0">
                            <button id="bulkActionsButton" data-dropdown-toggle="bulkActionsDropdown" class="btn-danger flex items-center justify-center gap-2 text-sm" type="button">
                                <i class="bi bi-trash"></i>
                                <span>{{ __('Bulk Actions') }} (<span x-text="selectedTasks.length"></span>)</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>

                            <!-- Bulk Actions dropdown menu -->
                            <div id="bulkActionsDropdown" class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                                <h6 class="mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Bulk Actions') }}</h6>
                                <ul class="space-y-2">
                                    <li class="cursor-pointer text-sm text-red-600 dark:text-red-400 hover:bg-gray-200 dark:hover:bg-gray-600 px-2 py-1 rounded"
                                        @click="bulkDeleteModalOpen = true">
                                        <i class="bi bi-trash mr-1"></i> {{ __('Delete Selected') }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex items-center justify-center gap-2">
                            <button id="priorityDropdownButton" data-dropdown-toggle="priorityDropdown" class="btn-default flex items-center justify-center gap-2" type="button">
                                <i class="bi bi-sliders"></i>
                                {{ __('Filter by Priority') }}
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div id="priorityDropdown" class="z-10 hidden w-56 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                                <ul class="space-y-2">
                                    <li class="cursor-pointer text-sm text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 px-2 py-1 rounded"
                                        onclick="handleFilter('', 'priority')">
                                        {{ __('All Priorities') }}
                                    </li>
                                    @foreach ($priorities as $key => $priority)
                                        <li class="cursor-pointer text-sm text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 px-2 py-1 rounded"
                                            onclick="handleFilter('{{ $key }}', 'priority')">
                                            {{ ucfirst($priority) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <button id="statusDropdownButton" data-dropdown-toggle="statusDropdown" class="btn-default flex items-center justify-center gap-2" type="button">
                                <i class="bi bi-sliders"></i>
                                {{ __('Filter by Status') }}
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div id="statusDropdown" class="z-10 hidden w-56 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                                <ul class="space-y-2">
                                    <li class="cursor-pointer text-sm text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 px-2 py-1 rounded"
                                        onclick="handleFilter('', 'status')">
                                        {{ __('All Statuses') }}
                                    </li>
                                    @foreach ($statuses as $key => $status)
                                        <li class="cursor-pointer text-sm text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 px-2 py-1 rounded"
                                            onclick="handleFilter('{{ $key }}', 'status')">
                                            {{ ucfirst($status) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    @can('task.view')
                    <a href="{{ route('admin.tasks.create') }}" class="btn-primary ml-2">
                        <iconify-icon icon="feather:plus" class="mr-2"></iconify-icon>
                        {{ __('New Task') }}
                    </a>
                    @endcan
                </div>
            </div>
            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto overflow-y-visible">
                <table id="dataTable" class="w-full dark:text-gray-400">
                    <thead class="bg-light text-capitalize">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th width="5%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        class="form-checkbox h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary dark:focus:ring-primary dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                        x-model="selectAll"
                                        @click="
                                            selectAll = !selectAll;
                                            selectedTasks = selectAll ?
                                                [...document.querySelectorAll('.user-checkbox')].map(cb => cb.value) :
                                                [];
                                        "
                                    >
                                </div>
                            </th>
                            <th width="20%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                <div class="flex items-center">
                                    {{ __('Title') }}
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => request()->sort === 'title' ? '-title' : 'title']) }}" class="ml-1">
                                        @if(request()->sort === 'title')
                                            <i class="bi bi-sort-alpha-down text-primary"></i>
                                        @elseif(request()->sort === '-title')
                                            <i class="bi bi-sort-alpha-up text-primary"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-gray-400"></i>
                                        @endif
                                    </a>
                                </div>
                            </th>
                            <th width="10%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                <div class="flex items-center">
                                    {{ __('Status') }}
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => request()->sort === 'status' ? '-status' : 'status']) }}" class="ml-1">
                                        @if(request()->sort === 'status')
                                            <i class="bi bi-sort-alpha-down text-primary"></i>
                                        @elseif(request()->sort === '-status')
                                            <i class="bi bi-sort-alpha-up text-primary"></i>
                                        @else
                                            <i class="bi bi-arrow-down-up text-gray-400"></i>
                                        @endif
                                    </a>
                                </div>
                            </th>
                            <th width="10%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                {{ __('Priority') }}
                                <a href="{{ request()->fullUrlWithQuery(['sort' => request()->sort === 'priority' ? '-priority' : 'priority']) }}" class="ml-1">
                                    @if(request()->sort === 'priority')
                                        <i class="bi bi-sort-alpha-down text-primary"></i>
                                    @elseif(request()->sort === '-priority')
                                        <i class="bi bi-sort-alpha-up text-primary"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up text-gray-400"></i>
                                    @endif
                                </a>
                            </th>
                            <th width="20%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Assigned to') }}</th>
                            @php ld_apply_filters('user_list_page_table_header_before_action', '') @endphp
                            <th width="15%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Action') }}</th>
                            @php ld_apply_filters('user_list_page_table_header_after_action', '') @endphp
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr class="{{ $loop->index + 1 != count($tasks) ?  'border-b border-gray-100 dark:border-gray-800' : '' }}">
                                <td class="px-5 py-4 sm:px-6">
                                    <input
                                        type="checkbox"
                                        class="user-checkbox form-checkbox h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary dark:focus:ring-primary dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                        value="{{ $task->id }}"
                                        x-model="selectedTasks"
                                    >
                                </td>
                                <td class="px-5 py-4 sm:px-6 flex items-center md:min-w-[200px]">
                                    <a data-tooltip-target="tooltip-user-{{ $task->id }}" href="{{ route('admin.tasks.edit', $task->id) }}" class="flex items-center">
                                        <div class="flex flex-col">
                                            <span>{{ $task->title }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $task->username }}</span>
                                        </div>
                                    </a>
                                    <div id="tooltip-user-{{ $task->id }}" href="{{ route('admin.tasks.edit', $task->id) }}" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                                        {{ __('Edit Task') }}
                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 sm:px-6">{{ $statuses[$task->status] ?? __('Unknown') }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ ucfirst($task->priority) }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $task->assigned->name ?? __('Unassigned') }}</td>
                                @php ld_apply_filters('user_list_page_table_row_before_action', '', $task) @endphp
                                <td class="px-5 py-4 sm:px-6 flex justify-center">
                                    <x-buttons.action-buttons :label="__('Actions')" :show-label="false" align="right">
                                        @can('task.edit')
                                        <x-buttons.action-item
                                            :href="route('admin.tasks.edit', $task->id)"
                                            icon="pencil"
                                            :label="__('Edit')"
                                        />
                                        @endcan

                                        @can('task.delete')
                                        <div x-data="{ deleteModalOpen: false }">
                                            <x-buttons.action-item
                                                type="modal-trigger"
                                                modal-target="deleteModalOpen"
                                                icon="trash"
                                                :label="__('Delete')"
                                                class="text-red-600 dark:text-red-400"
                                            />

                                            <x-modals.confirm-delete
                                                id="delete-modal-{{ $task->id }}"
                                                title="{{ __('Delete Task') }}"
                                                content="{{ __('Are you sure you want to delete this task?') }}"
                                                formId="delete-form-{{ $task->id }}"
                                                formAction="{{ route('admin.tasks.destroy', $task->id) }}"
                                                modalTrigger="deleteModalOpen"
                                                cancelButtonText="{{ __('No, cancel') }}"
                                                confirmButtonText="{{ __('Yes, Confirm') }}"
                                            />
                                        </div>
                                        @endcan
                                    </x-buttons.action-buttons>
                                </td>
                                @php ld_apply_filters('user_list_page_table_row_after_action', '', $task) @endphp
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('No tasks found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="my-4 px-4 sm:px-6">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>

    @can('task.delete')
    <!-- Bulk Delete Confirmation Modal -->
    <div
        x-cloak
        x-show="bulkDeleteModalOpen"
        x-transition.opacity.duration.200ms
        x-trap.inert.noscroll="bulkDeleteModalOpen"
        x-on:keydown.esc.window="bulkDeleteModalOpen = false"
        x-on:click.self="bulkDeleteModalOpen = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md"
        role="dialog"
        aria-modal="true"
        aria-labelledby="bulk-delete-modal-title"
    >
        <div
            x-show="bulkDeleteModalOpen"
            x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity"
            x-transition:enter-start="opacity-0 scale-50"
            x-transition:enter-end="opacity-100 scale-100"
            class="flex max-w-md flex-col gap-4 overflow-hidden rounded-lg border border-outline border-gray-100 dark:border-gray-800 bg-white text-on-surface dark:border-outline-dark dark:bg-gray-700 dark:text-gray-400"
        >
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-2 dark:border-gray-800">
                <div class="flex items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 p-1">
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
                <h3 id="bulk-delete-modal-title" class="font-semibold tracking-wide text-gray-800 dark:text-white">
                    {{ __('Delete Selected Tasks') }}
                </h3>
                <button
                    x-on:click="bulkDeleteModalOpen = false"
                    aria-label="close modal"
                    class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg p-1 dark:hover:bg-gray-600 dark:hover:text-white"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="1.4" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-4 text-center">
                <p class="text-gray-500 dark:text-gray-400">
                    {{ __('Are you sure you want to delete the selected tasks?') }}
                    {{ __('This action cannot be undone.') }}
                </p>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-gray-100 p-4 dark:border-gray-800">
                <form id="bulk-delete-form" action="{{ route('admin.tasks.bulk-delete') }}" method="POST">
                    @method('DELETE')
                    @csrf

                    <template x-for="id in selectedTasks" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>

                    <button
                        type="button"
                        x-on:click="bulkDeleteModalOpen = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                    >
                        {{ __('No, Cancel') }}
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-300 dark:focus:ring-red-800"
                    >
                        {{ __('Yes, Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endcan
</div>

@push('scripts')
<script>
    function handleFilter(value, sortKey = 'status') {
        let currentUrl = new URL(window.location.href);

        // Preserve sort parameter if it exists.
        const sortParam = currentUrl.searchParams.get('sort');

        // Reset the search params but keep the sort if it exists.
        currentUrl.search = '';

        if (value) {
            currentUrl.searchParams.set(sortKey, value);
        }

        // Re-add sort parameter if it existed.
        if (sortParam) {
            currentUrl.searchParams.set('sort', sortParam);
        }

        window.location.href = currentUrl.toString();
    }
</script>
@endpush
@endsection