<div>
    <div class="flex gap-4">
        <div class="flex flex-col w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PO
            </label>


            <input focus wire:model="paletBarcode" wire:keydown.debounce.150ms="paletBarcodeScan" type="text"
                id="paletBarcode"
                class=" w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

        </div>
        <div class="flex flex-col w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Surat Jalan
            </label>
            <input focus wire:model="paletBarcode" wire:keydown.debounce.150ms="paletBarcodeScan" type="text"
                id="paletBarcode"
                class=" w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

        </div>

        <div class="flex flex-col w-full">

            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Palet
            </label>
            <div class="w-full flex ">
                <select id="section" name="section" value="{{ old('section') }}"
                    class="mt-1 p-2 w-full border rounded-md focus:border-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors duration-300">
                    <option>L</option>
                </select>
                <input wire:model="trucking_id" type="text"
                    class="block w-full
                     p-4 text-gray-700 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
        </div>

        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Material No
            </label>
            <input wire:model="produkBarcode" wire:keydown.debounce.150ms="productBarcodeScan" type="text"
                id="produkBarcode"
                class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
    </div>
</div>
