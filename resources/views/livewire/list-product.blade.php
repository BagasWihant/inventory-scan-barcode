<div>
    <div class="mb-5">
        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PRODUK BARCODE </label>
        <input autofocus wire:model.live.debounce.150ms="search" type="text" id="large-input"
            class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
    </div>

    <div class="flex gap-4 overflow-x-auto shadow-lg sm:rounded-lg p-3 ">
        <div class="w-full">
            <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">Belum di Scan</h2>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Produk Barcode
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Kode Palet
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Nama Produk
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($product as $product)
                        <tr
                            class=" border rounded @if ($product->status == 1) bg-green-300 border-green-600 @else bg-white  dark:bg-gray-800 @endif dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->product_barcode }}</th>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->pallet_barcode }}</th>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->product_name }}</th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="w-full">
            <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">Berhasil di Scan</h2>

            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Produk Barcode
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Kode Palet
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Nama Produk
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productScanned as $product)
                        <tr
                            class=" border rounded @if ($product->status == 1) bg-green-300 dark:bg-green-500 @else bg-white  dark:bg-gray-800 @endif dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->product_barcode }}</th>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->pallet_barcode }}</th>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->product_name }}</th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
