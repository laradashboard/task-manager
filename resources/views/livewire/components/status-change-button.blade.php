<div x-data="{ open: false }" class="relative inline-block">
    <button
        @click="open = !open"
        type="button"
        class="badge
            {{ $status === 'completed' ? 'badge-success' : ($status === 'in_progress' ? 'badge-primary' : 'badge-warning') }}
            flex items-center gap-1"
    >
        {{ $statuses[$status] ?? __("Unknown") }}
        <iconify-icon
            icon="heroicons:chevron-down"
            class="w-3 h-3 crm:ml-3"
            :class="{ 'rotate-180': open }"
        ></iconify-icon>
        <span class="sr-only">{{ __("Change Status") }}</span>
    </button>

    <div
        x-show="open"
        @click.away="open = false"
        x-transition
        class="absolute z-10 mt-2 w-60 bg-white border border-gray-200 rounded shadow-lg"
    >
        @foreach($statuses as $key => $label)
            <button
                wire:click="changeStatusTo('{{ $key }}')"
                @click="open = false"
                class="block w-full text-left px-4 py-2 text-sm
                    {{ $status === $key ? 'font-bold bg-gray-100' : 'hover:bg-gray-50' }}
                    {{ $key === 'completed' ? 'text-green-700' : ($key === 'in_progress' ? 'text-blue-700' : 'text-yellow-700') }}"
                type="button"
                @if($status === $key) disabled @endif
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <span
        wire:loading
        wire:target="changeStatusTo"
        class="ml-2 text-gray-500 text-xs"
    >
        {{ __("Processing...") }}
    </span>
</div>