<div class="dark:text-white max-w-7xl mx-auto">

    <div class="text-2xl text-center font-extrabold py-6">Stock Material</div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg" wire:loading.remove>
        <div class="flex justify-between">

            <div class="">

                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" id="search" wire:model.live.debounce.300ms="searchKey"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search here..."  />
                </div>

            </div>
            <div class="">
                <button type="button" wire:click="exportExcel"
                    class="text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Excel</button>
                <button type="button" wire:click="exportPdf"
                    class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">PDF</button>
            </div>
        </div>

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Material No
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Location
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Qty
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">
                            {{ $d->material_no }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->locate }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->qty }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        <div wire:loading wire:target="exportExcel,exportPdf" aria-label="Loading..." role="status">
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
            <span class="text-4xl font-medium text-gray-500">Please wait...</span>
        </div>
    </div>

</div>

@script
    <script>
        $wire.on('searchFocus', (event) => {
            $("#search").focus()
        });
    </script>
@endscript
