<div>
    <div class="text-2xl font-extrabold py-6 text-center">Register Palet</div>

    <div class="flex justify-end">
        <a wire:navigate href="{{route('create_palet')}}" 
        class=" text-base text-white block bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-lg px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Buat Pallet Baru</a>
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
                            <button class="bg-blue-500  text-white font-bold py-2 px-4 rounded-lg">Detail</button>
                        </th>
                    </tr>
                @endforeach
            </tbody>
        </table>


        {{-- </div> --}}
    @endif
</div>