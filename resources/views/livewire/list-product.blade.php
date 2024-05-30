<div>
    <div class="flex gap-4">
        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Palet BARCODE
            </label>
            <input autofocus wire:model="paletBarcode" wire:keydown.debounce.150ms="paletBarcodeScan" type="text"
                id="paletBarcode"
                class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PRODUK BARCODE
            </label>
            <input wire:model="produkBarcode" wire:keydown.debounce.150ms="productBarcodeScan" type="text"
                id="produkBarcode"
                class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
    </div>

    <div class="flex justify-center">
        <div wire:loading wire:target="confirm,productBarcodeScan" aria-label="Loading..." role="status">
            <svg class="h-20 w-20 animate-spin stroke-gray-500" viewBox="0 0 256 256">
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
            <span class="text-4xl font-medium text-gray-500">Loading...</span>
        </div>
    </div>

    <div class="flex gap-4 overflow-x-auto sm:rounded-lg p-3 ">
        @if (count($productsInPalet) > 0)
            <div class="w-full">

                <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">List Barang </h2>
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Pallet No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Material No
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Pax
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Qty pickinglist
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productsInPalet as $product)
                            <tr class=" border rounded dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $product->pallet_no }}</th>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $product->material_no }}</th>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $product->pax }}</th>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $product->picking_qty }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- {{ $productsInPalet->links() }} --}}

            </div>
        @endif
        @if (count($scanned) > 0)
            <div class="w-full">
                <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">Berhasil di Scan</h2>
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Material No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                outstanding
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Qty received
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    keterangan
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- {{dd($scanned)}} --}}
                        @foreach ($scanned as $v)
                            <tr
                                class=" border rounded @if ($v->total == $v->counter) bg-green-300 dark:bg-green-500 @else bg-red-300 dark:bg-red-500 @endif  dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->material }}</th>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->sisa }} </th>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->counter }} </th>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if ($v->total == $v->counter)
                                        OK CONFIRM
                                    @else
                                        OUTSTANDING / NOT CLOSE
                                    @endif
                                </th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- {{ $scanned->links() }} --}}
            </div>
        @endif
    </div>
    <div class="flex justify-end pt-3">
        <button type="button" wire:click="resetPage"
            class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Reset</button>
        <button type="button" wire:click="confirm"
            class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">Konfimasi</button>
    </div>




</div>
@script
    <script>
        $wire.on('produkFocus', (event) => {
            $("#produkBarcode").focus()
        });
        $wire.on('paletFocus', (event) => {
            $("#paletBarcode").focus()
        });
        $wire.on('cannotScan', (event) => {
            alert('gak bisa scan')
            console.log('ass');
        });
    </script>
@endscript
