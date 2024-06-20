<div class="dark:text-white max-w-7xl mx-auto">
    <div class="text-2xl font-extrabold py-6 text-center">Input Stock Taking</div>

    <div class="grid grid-cols-2 gap-5">
        <div class="w-full " wire:ignore>
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hitung ke
            </label>
            <select
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full !p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @for ($index = 1; $index <= 3; $index++)
                    <option value="{{ $index }}">{{ $index }}</option>
                @endfor
            </select>
        </div>
        <div class="w-full" wire:ignore>
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Palet Code
            </label>
            <select id="materialselect"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full !p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected>Choose Material</option>
                @foreach ($listMaterial as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                @endforeach
            </select>
        </div>

    </div>

    <div class="grid grid-cols-2 gap-5">
        <div>
            <label for="first_name"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Location</label>
            <input type="text" id="disabled-input" wire:model="location" aria-label="disabled input"
                class="mb-6 bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                disabled>
        </div>
        <div>
            <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Qty</label>
            <input type="text" id="first_name" wire:model="qty"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Qty" />
        </div>
    </div>

    <div class="flex justify-end gap-5">
        @if ($materialCode)
            <button type="button" wire:click="save"
                class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">Save</button>
        @endif
        <button type="button" wire:click="cancel"
            class="text-white bg-gradient-to-r from-red-500 to-pink-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">Cancel</button>

    </div>
</div>


@script
    <script>
        $(document).ready(function() {
            $('#materialselect').select2({
                placeholder: "Material Code",
                width: 'resolve',
            });
            $('#materialselect').on('change', function(e) {
                @this.materialCode = e.target.value
                $wire.dispatch('materialChange')
            });


        });
    </script>
@endscript
