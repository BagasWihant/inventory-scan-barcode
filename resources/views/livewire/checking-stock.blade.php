<div class="dark:text-white max-w-7xl mx-auto">
    <div class="text-2xl font-extrabold py-6 text-center">Checking Stock</div>
    <div class="grid md:grid-cols-8 gap-3 w-full">

        <div class="col-span-2">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Palet Code
            </label>
            {{-- <input focus wire:model="paletBarcode" wire:keydown.debounce.250ms="paletBarcodeScan" type="text"
                id="paletBarcode"
                class=" w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

            <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select
                Pallet</label> --}}
            <select id="countries" wire:model="paletBarcode" wire:change="paletChange"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected>Choose Pallet</option>
                @foreach ($listPalet as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-span-2">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Material Code
            </label>
            <select id="countries" wire:model="materialCode"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="">All</option>
                @foreach ($listMaterial as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                @endforeach
            </select>
            {{-- <input focus wire:model="materialCode" wire:keydown.debounce.250ms="materialCodeChange" type="text"
                id="paletBarcode"
                class=" w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"> --}}
        </div>

        <div class="col-span-3">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date
            </label>
            <div class="flex items-center">
                <div class="relative" wire:ignore>

                    <input id="dtstart" type="date" wire:model="dateStart" onfocus="this.showPicker()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Select date start">
                </div>
                <span class="mx-4 text-gray-500">to</span>
                <div class="relative" wire:ignore>
                    <input id="dtend" type="date" wire:model="dateEnd" onfocus="this.showPicker()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Select date end">
                </div>
            </div>

        </div>
        {{-- <input type="text" wire:model="dateStart" id="ms">
        <input type="text" wire:model="dateEnd" id="me"> --}}


        <div class="col-span-1 flex justify-center items-end">
            <button wire:click="searching"
                class="relative inline-flex items-center justify-center p-0.5 mb-2 me-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-cyan-500 to-blue-500 group-hover:from-cyan-500 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">
                <span
                    class="relative px-5 py-2.5 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                    Search
                </span>
            </button>
        </div>
    </div>

    <div class=" grid grid-cols-2 justify-around">
        <div class="">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col " class="px-6 py-3">
                            Pallet No
                        </th>
                        <th scope="col " class="px-6 py-3">
                            Material No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Qty - [ PAX ]
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Location
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inStock as $v)
                        <tr class=" border rounded dark:border-gray-700">
                            <th scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->pallet_no }}</th>
                            <th scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->material_no }} </th>
                            <th scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->picking_qty }} - [ {{$v->pax}} ]
                            </th>
                            <th scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->locate }}
                            </th>
                            <th scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->status }}
                            </th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="">

            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col " class="px-6 py-3">
                            Pallet No
                        </th>
                        <th scope="col " class="px-6 py-3">
                            Material No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Qty - [ PAX ]
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Location
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                date
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shipped as $v)
                        <tr class=" border rounded dark:border-gray-700">
                            <th scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->pallet_no }}</th>
                            <th scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->material_no }} </th>
                            <th scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->picking_qty }} - [ {{$v->pax}} ]
                            </th>
                            <th scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->location_cd }}
                            </th>
                            <th scope="row"
                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $v->date }}
                            </th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
