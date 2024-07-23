<div class="dark:text-white max-w-7xl mx-auto">

    <div class="fixed left-0 top-0  h-screen w-screen flex justify-center items-center bg-slate-300/70 z-50"
        wire:loading.flex wire:target="searching">
        <div class="absolute animate-spin rounded-full h-32 w-32 border-t-4 border-b-4 border-purple-500"></div>
        <img src="https://www.svgrepo.com/show/509001/avatar-thinking-9.svg" class="rounded-full h-28 w-28">
    </div>

    <div class="text-2xl font-extrabold py-6 text-center">Receiving Report</div>
    <div class="grid md:grid-cols-8 gap-3 w-full pb-6">

        <div class="col-span-2" wire:ignore>
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Palet Code
            </label>
            <select id="paletselect"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full !p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
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
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected value="">All</option>
                @foreach ($listMaterial as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                @endforeach
            </select>

        </div>

        <div class="col-span-3">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date
            </label>
            <div class="flex items-center">
                <div class="relative" wire:ignore>

                    <input id="dtstart" type="date" wire:model="dateStart" onfocus="this.showPicker()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-1  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Select date start">
                </div>
                <span class="mx-4 text-gray-500">to</span>
                <div class="relative" wire:ignore>
                    <input id="dtend" type="date" wire:model="dateEnd" onfocus="this.showPicker()"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-1  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Select date end">
                </div>
            </div>
        </div>

        <div class="col-span-1 flex justify-center items-end">
            <button wire:click="searching"
                class="relative inline-flex items-center justify-center  overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-cyan-500 to-blue-500 group-hover:from-cyan-500 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">
                <span
                    class="relative px-5 py-1 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                    Show Data
                </span>
            </button>
        </div>
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
@script
    <script>
        $(document).ready(function() {
            $('#paletselect').select2();
            $('#paletselect').on('change', function(e) {
                @this.paletBarcode = e.target.value
                $wire.dispatch('paletChange')
            });
            $wire.on('popup', (event) => {
                Swal.fire({
                    timer: 1000,
                    title: event[0].title,
                    icon: "error",
                    showConfirmButton: false,
                    timerProgressBar: true,
                });


            })
        });
    </script>
@endscript
