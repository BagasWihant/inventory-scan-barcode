<div class="dark:text-white max-w-7xl mx-auto">
    <div class="text-2xl font-extrabold py-6 text-center">Result Stock Taking</div>

    <div class="grid grid-cols-3 pb-3">
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
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 shadow-lg">
        <thead class="text-xs border border-gray-300 text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr class=" ">
                <th scope="col" class="px-6 text-center py-3 border border-gray-300" rowspan="2">
                    Material No
                </th>
                <th scope="col" class="text-center py-1 border border-gray-300" colspan="2">
                    System
                </th>

                <th scope="col" class="text-center py-1 border border-gray-300" colspan="2">
                    Hitung 1
                </th>
                <th scope="col" class="text-center py-1 border border-gray-300" colspan="2">
                    Hitung 2
                </th>
                <th scope="col" class="text-center py-1 border border-gray-300" colspan="2">
                    Hitung 3
                </th>
                <th scope="col" class="text-center py-1 border border-gray-300" colspan="2">
                    Result
                </th>
            </tr>
            <tr>
                <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Loc s
                </th>
                <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Qty s
                </th>
                <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Loc
                </th>
                <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Qty
                </th>
                <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Loc
                </th>
                <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Qty
                </th>
                <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Loc
                </th>
                <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Qty
                </th>
                <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        +
                </th>
                <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        -
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $d)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $key }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $d['locsys'] ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d['qtysys'] ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d['loc1'] ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d['qty1'] ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d['loc2'] ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d['qty2'] ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d['loc3'] ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d['qty3'] ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d['plus'] ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d['min'] ?? ' - ' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
