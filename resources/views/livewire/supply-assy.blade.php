<div class="max-w-7xl m-auto">
    <div class="flex justify-between mb-3">
        <div class="flex gap-2">
            <div class="flex-col">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Issue Date</label>
                <input id="dtstart" type="date" wire:model.live="date" onclick="this.showPicker()"
                    @if ($topInputLock) readonly @endif
                    class="@if ($topInputLock) bg-gray-200 @else bg-gray-50 @endif  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Select date start">
            </div>
            <div class="flex-col">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Line</label>
                <input type="text" wire:model.live="line" disabled 
                class=" bg-gray-200  border border-gray-300 text-gray-900 text-sm rounded-lg ">
                {{-- <select type="text" wire:model="line" @if ($topInputLock) disabled @endif
                    class="@if ($topInputLock) bg-gray-200 @else bg-gray-50 @endif  border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    @foreach ($lines as $line)
                        <option value="{{ $line->line_c }}">{{ $line->line_c }}</option>
                    @endforeach
                </select> --}}
            </div>
        </div>

        <div class="">
            @if (!$btnSetup)
                <button type="button" id="showForm" wire:click="setup"
                    class="text-white bg-gradient-to-r from-teal-500 to-teal-600 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
                    Setup
                </button>
            @else
                <button type="button" id="showForm" wire:click="setupDone"
                    class="text-white bg-gradient-to-r from-green-600 to-green-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
                    Selesai Setup
                </button>
                <button type="button" id="showForm" wire:click="batal"
                    class="text-white bg-gradient-to-r from-red-600 to-red-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
                    Batal
                </button>
            @endif
        </div>
    </div>

    <div class="flex gap-4 mb-5">
        <label class="inline-flex items-center me-5 cursor-pointer">
            <input type="checkbox" value="1" wire:model.live="partial" class="sr-only peer" checked>
            <div
                class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
            </div>
            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Partial</span>
        </label>

        @if ($topInputLock)
            <div class="flex-col">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No Pallet</label>
                <input wire:model.live.debounce.300ms="noPallet" type="text"
                    class=" bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg "
                    placeholder="No Pallet (Press '/' to focus)" x-ref="search"
                    @keydown.window="
                if (event.keyCode === 191) {
                    event.preventDefault();
                    $refs.search.focus();
                }"
                    {{-- @focus="isVisible = true" @keydown.escape.window = "isVisible = false" @keydown="isVisible = true"
                @keydown.shift.tab="isVisible = false" --}}>

                @if ($optionPalletShow)
                    <div class="absolute z-50 bg-gray-50 text-xs rounded-lg shadow-lg w-[13.5em] mt-1">
                        @if (count($collectPallet) > 0)
                            <ul>
                                @foreach ($collectPallet as $list)
                                    <li class="border-b border-gray-400 rounded-lg">
                                        <span {{-- x-on:click="$wire.set('noPallet', '{{ $list }}');
                                    $wire.set('optionPalletShow', false);" --}} wire:click="setPallet('{{ $list }}')"
                                            class="hover:bg-gray-300 rounded-lg flex items-center transition ease-in-out duration-150 px-3 py-3">{{ $list }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="px-3 py-3">No results for "{{ $noPallet }}"</div>
                        @endif
                    </div>
                @endif
            </div>
            @if ($inputMaterialNo)
                <div class="flex-col">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Material No</label>
                    <input type="text" wire:model.live.debounce.300ms="materialNo"
                        class=" bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg "
                        placeholder="Material No">

                    @if ($optionMaterialShow)
                        <div class="absolute z-50 bg-gray-50 text-xs rounded-lg shadow-lg w-[13.5em] mt-1">
                            @if (count($collectMaterial) > 0)
                                <ul>
                                    @foreach ($collectMaterial as $list)
                                        <li class="border-b border-gray-400 rounded-lg">
                                            <span wire:click="setMaterialNo('{{ $list }}')"
                                                class="hover:bg-gray-300 rounded-lg flex items-center transition ease-in-out duration-150 px-3 py-3">{{ $list }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="px-3 py-3">No results for "{{ $materialNo }}"</div>
                            @endif
                        </div>
                    @endif

                </div>
            @endif
        @endif

    </div>




    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Material No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Qty
                    </th>
                    <th scope="col" class="px-6 py-3">
                        -
                    </th>
                    <th scope="col" class="px-6 py-3">
                        -
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <span class="sr-only">Edit</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataTable as $data)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $data->material_no }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $data->qty }}
                        </td>
                        <td class="px-6 py-4">
                            --
                        </td>
                        <td class="px-6 py-4">
                            --
                        </td>
                        <td class="px-6 py-4 text-right">
                            {{-- <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@script
    <script>
        $wire.on('notification', (prop) => {
            Swal.fire({
                timer: 1000,
                title: prop[0].title,
                icon: prop[0].icon,
                showConfirmButton: false,
                timerProgressBar: true,
            });
        })
    </script>
@endscript
