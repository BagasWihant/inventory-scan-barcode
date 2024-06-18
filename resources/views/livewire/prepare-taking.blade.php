<div class="dark:text-white max-w-7xl mx-auto">
    <div class="text-2xl font-extrabold pt-6 pb-1 text-center">Prepare Stock Taking</div>

    <span class="text-xl fonr-medium flex justify-center pb-7">All Menu is
        <span class="font-bold px-3">{{ $statusActive ? 'Disable' : 'Accessible' }}</span>
    </span>

    <div class="mx-auto max-w-xl">
        <div class="mb-2 text-center">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status
                Active</label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model="statusActive" wire:click="changeStatusActive" class="sr-only peer">
                <div
                    class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                </div>
            </label>
        </div>
        <div class="mb-5" wire:ignore>
            <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date
                disable</label>
            <input type="date" id="date" wire:model="date" onfocus="this.showPicker()"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                required />
        </div>

        <button type="button" wire:click="savedata"
            class="text-white block bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save</button>
    </div>

</div>
