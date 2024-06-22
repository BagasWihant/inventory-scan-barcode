<div class="dark:text-white max-w-7xl mx-auto">
    <div class="text-2xl font-extrabold py-6 text-center">Result Stock Taking</div>

    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
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
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
