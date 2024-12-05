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

            <input wire:model.live.debounce.400ms="materialNo" type="text" placeholder="Material No"
                class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

            <div class="absolute z-10 w-1/4">
                <div class="py-3 text-center bg-green-100 text-green-700 rounded-lg" wire:loading.block
                    wire:target="materialNo">Searching</div>
                <div wire:loading.remove class="rounded-lg bg-slate-50 shadow">

                    @if (strlen($materialNo) >= 3 && count($selectedData) == 0)
                        @forelse ($resultSearchMaterial as $res)
                            <div class="py-1 px-3 text-base hover:bg-blue-200 rounded-lg" role="button"
                                x-data="{
                                    updateDetails(data) {
                                        $wire.set('selectedData', data);
                                        $refs.requestQty.focus();
                                    }
                                }" @click="updateDetails('{{ json_encode($res) }}')">
                                {{ $res->matl_no }}
                            </div>
                        @empty
                            <div class="py-3 text-center text-base bg-red-200 rounded-lg">Tidak Ditemukan</div>
                        @endforelse
                    @endif
                </div>
            </div>

            <div class="flex gap-4 my-1">
                <input wire:model="requestQty" x-ref="requestQty" type="text" wire:keydown.enter="saveRequest" placeholder="Request Qty"
                    class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                <input wire:model="selectedData.bag_qty" readonly placeholder="Bag. Qty" type="text"
                    class="block w-full p-2 text-gray-900 border border-gray-300 bg-slate-200 rounded-lg  text-base">
                <input wire:model="selectedData.iss_min_lot" readonly placeholder="Min. Lot" type="text"
                    class="block w-full p-2 text-gray-900 border border-gray-300 bg-slate-200 rounded-lg  text-base">
                <input wire:model="selectedData.iss_unit" readonly placeholder="Unit" type="text"
                    class="block w-full p-2 text-gray-900 border border-gray-300 bg-slate-200 rounded-lg  text-base">
            </div>


        </div>
        <div class="w-2/3 bg-gray-200 rounded-md">
            <strong class="flex justify-center">Total Qty Request<span>&nbsp;{{ $totalRequest['qty'] }}</span></strong>
            <div wire:poll.4s="streamTableSum" class="relative overflow-y-auto shadow-md rounded-lg max-h-40">
                <table class="w-full text-xs text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs sticky top-0 text-gray-700 bg-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Transaction No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Jumlah Material
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Time Request
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($totalRequest['data'] as $tr)
                            <tr wire:key="material-request-{{ $loop->iteration }}"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $tr->transaksi_no }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $tr->count }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ (int) $tr->time_request }} mnt
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


        </div>
    </div>


    {{-- table --}}
    <div class="relative overflow-x-auto shadow-md rounded-lg my-4">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                <tr>
                    <th scope="col" class="px-1 py-3">
                        No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Material No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Material Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Qty Request
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Bag. Qty
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Min Lot
                    </th>
                    <th scope="col" class="px-6 py-3">
                        User Request
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($materialRequest as $m)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">
                            {{ $loop->iteration }}
                        </td>
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $m->material_no }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $m->material_name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $m->request_qty }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $m->bag_qty }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $m->iss_min_lot }}
                        </td>
                        <td class="px-6 py-4" x-data="{
                            editedUser: false,
                            value: '{{ $m->user_request }}',
                            oldValue: '{{ $m->user_request }}'
                        }" @click="editedUser = true">
                            <template x-if="editedUser">
                                <input type="text" x-model="value" wire:model="userRequest"
                                    placeholder="User Request"
                                    @keydown.enter="
                                           editedUser = false; 
                                           $wire.updateUserRequest('{{ $m->id }}');
                                       "
                                    @keydown.escape="
                                           editedUser = false;
                                           value = oldValue;
                                       "
                                    @focus="$event.target.select()" x-ref="input"
                                    class="p-1 text-gray-900 border border-gray-300 bg-slate-200 rounded-lg text-xs">
                            </template>

                            <template x-if="!editedUser">
                                <span @click="editedUser = true;">{{ $m->user_request }}</span>
                            </template>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="flex justify-end my-3">
        <button class="btn bg-blue-500 shadow-md text-white p-2 rounded-lg" wire:click="submitRequest">Submit
            Request</button>
    </div>

    <div wire:loading.flex
        class=" fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        wire:target="updateUserRequest,saveRequest" aria-label="Loading..." role="status">
        <svg class="h-20 w-20 animate-spin stroke-white " viewBox="0 0 256 256">
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
        <span class="text-4xl font-medium text-white">{{ $statusLoading ?? 'Loading...' }}</span>
    </div>
    @script
        <script>
            $wire.on('alert', (event) => {
            Swal.fire({
                timer: event[0].time,
                title: event[0].title,
                icon: event[0].icon,
                showConfirmButton: false,
                timerProgressBar: true,
            });
        });
        </script>
    @endscript
</div>
