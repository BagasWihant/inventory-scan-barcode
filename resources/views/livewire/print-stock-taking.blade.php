<div class="dark:text-white max-w-7xl mx-auto">
    <div class="text-2xl font-extrabold pt-6 pb-1 text-center">Print Stock Taking</div>


    

    @if ($listData->count() > 0)
        
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Material No
                    </th>
                   
                </tr>
            </thead>
            <tbody>
                @foreach ($listData as $data)
                    
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$loop->iteration}}
                    </th>
                    <td class="px-6 py-4">
                        {{$data->material_no}}
                    </td>
                </tr> 
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-2xl font-extrabold pt-6 pb-1 text-center">No Data</div>
    @endif



</div>
