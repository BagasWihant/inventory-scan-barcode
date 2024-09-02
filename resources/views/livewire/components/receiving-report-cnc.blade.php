<div>
    <div class="flex gap-3 w-full pb-6">

        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Trucking ID
            </label>
            <input wire:model.live="truckingId" type="text" autocomplete="off"  @if ($truckingDisable) disabled @endif
                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg   text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

            <div class="">
                <div class="py-3 text-center bg-green-100 text-green-700 rounded-lg" wire:loading.block
                    wire:target="truckingId">Searching</div>
                <div wire:loading.remove class="rounded-lg bg-slate-50 shadow absolute">

                    @if (strlen($truckingId) >= 2 && $listTruck != 'kosong')
                        @forelse ($listTruck as $p)
                            <div class="py-1 px-3 text-base hover:bg-blue-200 rounded-lg" role="button"
                                wire:click="chooseTrucking('{{ $p }}')">{{ $p }}
                            </div>
                        @empty
                            <div class="py-3 text-center text-base bg-red-200 rounded-lg">Tidak Ditemukan</div>
                        @endforelse
                    @endif
                </div>
            </div>
        </div>

        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Palet
                Code
            </label>
            <input wire:model.live="searchPalet" type="text" autocomplete="off"  @if ($paletDisable) disabled @endif
                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg   text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

            <div class="">
                <div class="py-3 text-center bg-green-100 text-green-700 rounded-lg" wire:loading.block
                    wire:target="paletChange">Searching</div>
                <div wire:loading.remove class="rounded-lg bg-slate-50 shadow absolute">

                    @if (strlen($searchPalet) >= 2 && $listPalet != 'kosong')
                        @forelse ($listPalet as $p)
                            <div class="py-1 px-3 text-base hover:bg-blue-200 rounded-lg" role="button"
                                wire:click="choosePalet('{{ $p->pallet_no }}')">{{ $p->pallet_no }}
                            </div>
                        @empty
                            <div class="py-3 text-center text-base bg-red-200 rounded-lg">Tidak Ditemukan</div>
                        @endforelse
                    @endif
                </div>
            </div>
        </div>

        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Material
                Code
            </label>
            <select id="countries" wire:model="materialCode"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="">All</option>
                {{-- @if (count($listMaterial) > 0) --}}
                {{-- @endif --}}
                @foreach ($listMaterial as $p)
                    <option value="{{ $p->material_no }}">{{ $p->material_no }}</option>
                @endforeach
            </select>

        </div>

        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date
            </label>
            <div class="flex items-center">
                <div class="relative" wire:ignore>

                    <input id="dtstart" type="date" wire:model="dateStart" onfocus="this.showPicker()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Select date start">
                </div>
                <span class="mx-2 text-gray-500">to</span>
                <div class="relative" wire:ignore>
                    <input id="dtend" type="date" wire:model="dateEnd" onfocus="this.showPicker()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Select date end">
                </div>
            </div>
        </div>

        <div class="w-full flex items-end">
            <button wire:click="showData()"
                class="relative inline-flex items-center justify-center  overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-cyan-500 to-blue-500 group-hover:from-cyan-500 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">
                <span
                    class="relative px-5 py-2 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                    Show Data
                </span>
            </button>
        </div>
    </div>
    <div class="flex justify-between">
        @if ($inputDisable)
            <button wire:click="resetData()"
                class="relative inline-flex items-center justify-center  overflow-hidden text-sm font-medium text-white rounded-lg group bg-red-600">
                <span class="relative px-5 py-2 transition-all ease-in duration-75 rounded-md group-hover:bg-opacity-0">
                    Clear Searching
                </span>
            </button>
        @endif
        @if ($exportDisable)
            <button wire:click="export('xls')"
                class="relative inline-flex items-center justify-center  overflow-hidden text-sm font-medium text-white rounded-lg group bg-green-600">
                <span class="relative px-5 py-2 transition-all ease-in duration-75 rounded-md group-hover:bg-opacity-0">
                    Export Excel
                </span>
            </button>
        @endif
    </div>

    @if (count($receivingData) > 0)
        <div class=" grid ">
            <div class="">
                <span class="text-gray-900 flex justify-center font-semibold">Stock Material</span>
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 shadow-md">
                    <thead class="text-xs text-gray-800 uppercase bg-slate-200 dark:text-gray-400">
                        <tr>
                            <th scope="col " class="px-6 py-3">
                                Date SIWS
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Received Date
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Trucking ID
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Kit No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Pallet No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Material No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Qty Deliv SIWS
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Qty Receive KIAS
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-slate-50">
                        @foreach ($receivingData as $v)
                            <tr class=" border rounded dark:border-gray-700">

                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->Delivery_Supply_Date_SIWS }} </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->Received_Date }}
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->trucking_id }}
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->kit_no }}
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->pallet_no }}
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->material_no }}
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->Qty_Delivery_SIWS }}
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->Qty_Received_KIAS }}
                                </th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
