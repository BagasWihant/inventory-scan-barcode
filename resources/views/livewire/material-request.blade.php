<div class="max-w-7xl m-auto">
    <div class="flex gap-3">
        {{-- input --}}
        <div class=" w-full">

            <div class="flex gap-4">
                <div class="flex items-center px-2 border border-gray-200 rounded dark:border-gray-700">
                    <input id="bordered-radio-1" type="radio" value="1" wire:model="type"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="bordered-radio-1"
                        class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Reguler</label>
                </div>
                <div class="flex items-center px-2 border border-gray-200 rounded dark:border-gray-700">
                    <input checked id="bordered-radio-2" type="radio" value="2" wire:model="type"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="bordered-radio-2"
                        class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Urgent</label>
                </div>
            </div>

            <input wire:model.live.debounce.400ms="searchMaterialNo" type="text" placeholder="Material No"
                class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

            <div class="flex gap-4 my-1">
                <input wire:model="requestQty" type="text" wire:keydown.enter="saveRequest" placeholder="Request Qty"
                    class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                <input wire:model="" readonly placeholder="Bag. Qty" type="text"
                    class="block w-full p-2 text-gray-900 border border-gray-300 bg-slate-200 rounded-lg  text-base">
                <input wire:model="" readonly placeholder="Min. Lot" type="text"
                    class="block w-full p-2 text-gray-900 border border-gray-300 bg-slate-200 rounded-lg  text-base">
                <input wire:model="" readonly placeholder="Unit" type="text"
                    class="block w-full p-2 text-gray-900 border border-gray-300 bg-slate-200 rounded-lg  text-base">
            </div>


        </div>
        <div class="w-2/3 bg-gray-200 rounded-md">
            <strong class="flex justify-center">Total Qty Request<span>&nbsp;{{ $totalQtyRequest }}</span></strong>
            <div class="relative overflow-y-auto shadow-md rounded-lg max-h-40">
                <table class="w-full text-xs text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs sticky top-0 text-gray-700 bg-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Transaction No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Time Request
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Submit By
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Apple MacBook Pro 17"
                            </th>
                            <td class="px-6 py-4">
                                Silver
                            </td>
                            <td class="px-6 py-4">
                                Laptop
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>
