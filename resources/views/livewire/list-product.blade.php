<div>
    <div class="mb-5">
        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Palet BARCODE
        </label>
        <input autofocus wire:model.live.debounce.300ms="paletBarcode" type="text"
            class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
    </div>
    <div class="mb-5">
        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PRODUK BARCODE
        </label>
        <input wire:model="produkBarcode" wire:keyup.debounce.50ms="productBarcodeScan" type="text" id="produkBarcode"
            class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
    </div>

    <div class="flex gap-4 overflow-x-auto shadow-lg sm:rounded-lg p-3 ">
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
                                Picking Qty
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productsInPalet as $product)
                        <tr class=" border rounded dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->pallet_barcode }}</th>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->material_no }}</th>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->pax }}</th>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->qty }}</th>
                        </tr>
                    @endforeach
                </tbody>
                {{ $productsInPalet->links() }}
            </table>
        </div>
        <div class="w-full">
            <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">Berhasil di Scan</h2>

            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Material No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Qty Sisa
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Qty Stok
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($scanned as $v)
                        <tr
                            class=" border rounded @if ($v->total == $v->counter) bg-green-300 dark:bg-green-500 @else bg-red-300 dark:bg-red-500  @endif  dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->id }}</th>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->sisa }} </th>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->counter }} </th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $scanned->links() }}
        </div>
    </div>
</div>
@script
    <script>
        $wire.on('produkFocus', (event) => {
            $("#produkBarcode").focus()
        });
    </script>
@endscript
