<form action="{{ $action }}" method="POST">
    @method($method ?? 'POST')
    @csrf
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <label for="title" class="form-label">{{ __('Task Title') }}</label>
            <input type="text" name="title" id="title" required autofocus value="{{ old('title', $task->title ?? '') }}" placeholder="{{ __('Enter Task Title') }}" class="form-control">
        </div>
        <div>
            <x-inputs.combobox
                name="priority"
                label="{{ __('Priority') }}"
                placeholder="{{ __('Select Priority') }}"
                :options="collect($priorities)->map(fn($name, $id) => ['value' => $id, 'label' => ucfirst($name)])->values()->toArray()"
                :selected="old('priority', $task->priority ?? '')"
                :searchable="false"
            />
        </div>
        <div>
            <x-inputs.combobox
                name="status"
                label="{{ __('Status') }}"
                placeholder="{{ __('Select Status') }}"
                :options="collect($statuses)->map(fn($name, $id) => ['value' => $id, 'label' => ucfirst($name)])->values()->toArray()"
                :selected="old('status', $task->status ?? '')"
                :searchable="false"
            />
        </div>
        <div>
            <x-inputs.combobox
                name="assigned_to"
                label="{{ __('Assigned To') }}"
                placeholder="{{ __('Select User') }}"
                :options="collect($users)->map(fn($name, $id) => ['value' => $id, 'label' => ucfirst($name)])->values()->toArray()"
                :selected="old('assigned_to', $task->assigned_to ?? '')"
                :searchable="true"
            />
        </div>
    </div>
    <div class="mt-4">
        <label for="description" class="form-contorl">{{ __('Description') }}</label>
        <textarea name="description" id="description" rows="10">{!! old('description', $task->description ?? '') !!}</textarea>
    </div>
    <div class="mt-6 flex justify-start gap-4">
        <button type="submit" class="btn-primary">{{ __('Save') }}</button>
        <a href="{{ route('admin.tasks.index') }}" class="btn-default">{{ __('Cancel') }}</a>
    </div>
</form>