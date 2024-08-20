<div>
    <div class="text-2xl font-extrabold py-6 text-center">Register Palet</div>

    <div class="flex justify-end">
        <a wire:navigate href="{{ route('create_palet') }}"
            class=" text-base text-white block bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-lg px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Buat
            Pallet Baru</a>
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

        <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">List Barang </h2>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Tanggal Scan
                    </th>
                    <th scope="col" class="px-6 py-3">
                        No Pallet
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Line C
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Tanggal Supply
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3">

                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listMaterial as $product)
                    <tr class=" border rounded dark:border-gray-700 ">
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->created_at }}</th>

                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->palet_no }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->line_c }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->supply_date }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->status }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <button class="bg-blue-500  text-white font-bold py-2 px-4 rounded-lg"
                                data-modal-target="static-modal" data-modal-toggle="static-modal"
                                wire:click="detail('{{ $product->palet_no }}')">Detail</button>
                        </th>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" wire:ignore.self
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <div class="flex justify-between w-full font-bold">
                            <p class="font-bold">{{$no_palet_modal}}</p>
                            <strong class=" ">{{$scan_date_modal}} </strong>
                        </div>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="static-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-2">
                        @if (count($listDetail) > 0)
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        No
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Material no
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Pack
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Total Qty
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listDetail as $d)
                                    <tr class=" border rounded dark:border-gray-700 ">
                                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $loop->iteration }}</th>
                
                                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $d->material_no }}
                                        </th>
                                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $d->pack }}
                                        </th>
                                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $d->total_qty }}
                                        </th>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                    <!-- Modal footer -->
                    <div class="flex items-center justify-end p-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button data-modal-hide="static-modal" type="button"
                            class="py-2 px-3 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
    <script>
        $wire.on('detailShow', (event) => {
            Swal
        })
    </script>
@endscript
