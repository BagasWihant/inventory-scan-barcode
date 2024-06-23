<div class="dark:text-white max-w-7xl mx-auto">
    <div class="text-2xl font-extrabold py-6 text-center">Stock Taking Confirmation</div>

    <div class="flex justify-end" >
        <button type="button" id="hideForm" 
            class="text-white bg-gradient-to-r from-red-500 to-pink-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
            Print
        </button>
        <button type="button" id="showForm" wire:click="confirm"
            class="text-white bg-gradient-to-r from-green-500 to-teal-400 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
            Confirmation
        </button>

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
                    STO
                </th>
                <th scope="col" class="px-6 text-center py-3 border border-gray-300" rowspan="2">
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
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $d->material_no }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $d->locsys ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->qtysys ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->loc ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->qty ?? ' - ' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->result ?? ' - ' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
