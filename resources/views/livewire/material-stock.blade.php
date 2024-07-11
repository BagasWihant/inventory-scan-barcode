<div class="dark:text-white max-w-7xl mx-auto">

    <div class="text-2xl text-center font-extrabold py-6" id="setHerePagination">Stock Material</div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <div class="flex justify-between" >

            <div class="flex gap-4">

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
                        placeholder="Search here..." />
                </div>

                <div class="w-1/3" wire:ignore>
                    <select id="materialselect" style="width: 100%" wire:model="perPage" wire:change="setPerPage"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full !p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>

            </div>
            <div class="">
                <button type="button" wire:click="exportExcel"
                    class="text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Excel</button>
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
            <tbody wire:ignore.self>
                @foreach ($datas as $d)
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
    {{ $datas->links(data: ['scrollTo' => '#setHerePagination']) }}

    <div class="fixed left-0 top-0  h-screen w-screen flex justify-center items-center bg-slate-300/70 z-50"
        wire:loading.flex wire:target="exportExcel,exportPdf,gotoPage,nextPage,previousPage,setPerPage">
        <div class="absolute animate-spin rounded-full h-32 w-32 border-t-4 border-b-4 border-purple-500"></div>
        <img src="https://www.svgrepo.com/show/509001/avatar-thinking-9.svg" class="rounded-full h-28 w-28">
    </div>

</div>

@script
    <script>
        $wire.on('searchFocus', (event) => {
            $("#search").focus()
        });
    </script>
@endscript
