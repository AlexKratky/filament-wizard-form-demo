<div class="p-4 bg-white rounded-lg border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Live Counter Component</h3>

    <div class="flex items-center gap-4">
        <button
            wire:click="decrement"
            type="button"
            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
        >
            -
        </button>

        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $count }}</span>

        <button
            wire:click="increment"
            type="button"
            class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600"
        >
            +
        </button>
    </div>
</div>
