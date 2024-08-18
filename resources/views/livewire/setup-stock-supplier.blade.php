<div>
    <div class="flex gap-4">



        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pallet No
            </label>
            <input wire:model="searchPalet" wire:keydown.debounce.300ms="paletChange" type="text" id="produkBarcode"
                @if ($paletDisable) disabled @endif autocomplete="off"
                class="  @if ($paletDisable) bg-gray-100 @endif block w-full p-2 text-gray-900 border border-gray-300 rounded-lg   text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

            <div class="absolute contents">
                <div class="py-3 text-center bg-green-100 text-green-700 rounded-lg" wire:loading.block
                    wire:target="paletChange">Searching</div>
                <div wire:loading.remove class="rounded-lg bg-slate-50 shadow">

                    @if (strlen($searchPalet) >= 2)
                        @forelse ($listPallet as $p)
                            <div class="py-1 px-3 text-base hover:bg-blue-200 rounded-lg" role="button"
                                wire:click="choosePalet('{{ $p->pallet_no }}')">{{ $p->pallet_no }}
                            </div>
                        @empty
                            @if (!$paletDisable)
                                <div class="py-3 text-center text-base bg-red-200 rounded-lg">Tidak Ditemukan</div>
                            @endif
                        @endforelse
                    @endif
                </div>
            </div>

        </div>

        <div class="w-1/4">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white ">Setup By
            </label>
            <input wire:model="input_setup_by" disabled
                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-100 text-base">
        </div>

        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Material No
            </label>
            <input wire:model="material_no" wire:keydown.debounce.150ms="materialNoScan" type="text"
                id="materialNoScan"
                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
    </div>
    <div wire:loading.flex
        class=" fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        wire:target="materialNoScan,resetPage,confirm,choosePo" aria-label="Loading..." role="status">
        <svg class="h-20 w-20 animate-spin stroke-white " viewBox="0 0 256 256">
            <line x1="128" y1="32" x2="128" y2="64" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="24"></line>
            <line x1="195.9" y1="60.1" x2="173.3" y2="82.7" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="24"></line>
            <line x1="224" y1="128" x2="192" y2="128" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="24">
            </line>
            <line x1="195.9" y1="195.9" x2="173.3" y2="173.3" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="24"></line>
            <line x1="128" y1="224" x2="128" y2="192" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="24">
            </line>
            <line x1="60.1" y1="195.9" x2="82.7" y2="173.3" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="24"></line>
            <line x1="32" y1="128" x2="64" y2="128" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="24"></line>
            <line x1="60.1" y1="60.1" x2="82.7" y2="82.7" stroke-linecap="round"
                stroke-linejoin="round" stroke-width="24">
            </line>
        </svg>
        <span class="text-4xl font-medium text-white">Loading</span>
    </div>

    @if (count($listMaterial) > 0)
        {{-- <div class="flex  gap-4 overflow-x-auto sm:rounded-lg p-3 "> --}}
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            wire:click="resetPage">Reset</button>
        <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">List Barang </h2>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Material No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Kit No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Line C
                    </th>
                    <th scope="col" class="px-6 py-3">
                        QTY Picking
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Location
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listMaterial as $product)
                    <tr class=" border rounded dark:border-gray-700 ">
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->material_no }}</th>

                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->kit_no }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->line_c }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->picking_qty }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->locate }}
                        </th>
                    </tr>
                @endforeach
            </tbody>
        </table>


        {{-- </div> --}}
    @endif
</div>
