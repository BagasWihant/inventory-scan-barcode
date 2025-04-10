<div class="max-w-7xl m-auto">
    <div class="flex gap-3">
        {{-- input --}}
        <div class=" w-full" x-data="{
            reqQty: '',
            selectedData: null,
            multiply() {
                const minLot = this.selectedData.iss_min_lot;
                console.log(minLot, this.$refs.requestQty.value);
                if (minLot != 1 && this.$refs.requestQty.value > 99) {
                    Swal.fire({
                        timer: 1500,
                        title: 'Maks 99',
                        icon: 'error',
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                    this.$refs.requestQty.value = 99
                }
                if (this.$refs.requestQty) {
                    const qtyy = this.$refs.requestQty.value;
                    this.reqQty = minLot * qtyy;
                }
            },
            handleSave() {
                $wire.saveRequest(this.reqQty).then(res => {
                    if (res) {
                        this.handleCancel();
                    } else {
                        this.reqQty = '';
                        this.$refs.requestQty.value = '';
                    }
                })
        
            },
            handleCancel() {
                this.selectedData = null;
                this.reqQty = '';
                this.$refs.requestQty.value = '';
            }
        }">

            <div class="flex justify-between gap-2 flex-shrink-0">
                <div class="flex gap-4">
                    <div class="flex items-center px-2 rounded">
                        <input id="bordered-radio-1" type="radio" value="1" wire:model="type"
                            @if ($userRequestDisable == true) disabled @endif
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="bordered-radio-1"
                            class="w-full py-4 ms-2 text-sm font-medium @if ($userRequestDisable == true) text-gray-600 @else  text-gray-900 @endif dark:text-gray-300">Reguler</label>
                    </div>
                    <div class="flex items-center px-2 rounded ">
                        <input checked id="bordered-radio-2" type="radio" value="2" wire:model="type"
                            @if ($userRequestDisable == true) disabled @endif
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="bordered-radio-2"
                            class="w-full py-4 ms-2 text-sm font-medium @if ($userRequestDisable == true) text-gray-600 @else  text-gray-900 @endif dark:text-gray-300">Urgent</label>
                    </div>
                    {{-- <input wire:model="userRequest" type="text" placeholder="User Request"
                        @if ($userRequestDisable == true) disabled @endif
                        class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"> --}}
                    <div class="">
                        <label for="">Issue Date</label>
                        <input wire:change="dateDebounce" wire:model='date' type="date"
                            @if ($userRequestDisable == true) disabled @endif onfocus="this.showPicker()"
                            class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    <div class="">
                        <select wire:model="line_c" wire:change="lineChange"
                            class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Line Code</option>
                            @foreach ($listLine as $p)
                                <option value="{{ $p->line_c }}">{{ $p->line_c }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="">

                    @if ($userRequestDisable == true)
                        <button
                            class="btn bg-yellow-500 shadow-md text-white px-2 p-1 m-1 rounded-lg text-xs text-nowrap"
                            @click="@this.set('userRequestDisable', false)">Ganti</button>
                    @endif
                    <button class="btn bg-red-500 shadow-md text-white px-2 py-1 m-1 rounded-lg text-sm"
                        @click="handleCancel" wire:click="resetField">Clear</button>
                </div>

            </div>
            <div class="flex gap-3">
                <select wire:model="searchMaterialNo" wire:change="selectedDataDebounce"
                    class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Material No</option>
                    @foreach ($listMaterialNo as $p)
                        <option value="{{ json_encode($p) }}">{{ $p->material_no }}</option>
                    @endforeach
                </select>
            </div>


            <div class="flex gap-4 my-1">
                {{-- <input x-ref="requestQty" type="text" @keydown.enter="handleSave" x-on:input="multiply()"
                    placeholder="Input Pax"
                    class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"> --}}

                <input wire:model="selectedData.req_qty" readonly placeholder="Request Qty" type="text"
                    class="block w-full p-2 text-gray-900 border border-gray-300 bg-slate-200 rounded-lg  text-base">
                <input wire:model="selectedData.qty" readonly placeholder="Qty" type="text"
                    class="block w-full p-2 text-gray-900 border border-gray-300 bg-slate-200 rounded-lg  text-base">
                <input wire:model="selectedData.iss_min_lot" readonly placeholder="Min. Lot" type="text"
                    class="block w-full p-2 text-gray-900 border border-gray-300 bg-slate-200 rounded-lg  text-base">
                <input wire:model="selectedData.iss_unit" readonly placeholder="Unit" type="text"
                    class="block w-full p-2 text-gray-900 border border-gray-300 bg-slate-200 rounded-lg  text-base">
            </div>
            {{-- <span class="text-red-600">Pastikan <strong>Request Qty</strong> sesuai dengan kelipatan <strong>Min.
                    Lot</strong></span> --}}

            <button class="btn bg-blue-500 shadow-md text-white p-2 rounded-lg" wire:click="saveRequest">Tambah
                material</button>

        </div>
        <div class="w-2/3 bg-gray-200 rounded-md">
            <strong class="flex justify-center">Total Qty Request<span>&nbsp;{{ $totalRequest['qty'] }}</span></strong>
            <div wire:poll.4s="streamTableSum" wire:key="polling-table"
                class="relative overflow-y-auto shadow-md rounded-lg max-h-40">
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
                            <th class="px-6 py-3">

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
                                <td>
                                    <button class="p-1 text-sm bg-red-500 rounded-xl text-white"
                                        wire:click="cancelTransaksi('{{ $tr->transaksi_no }}')">Cancel</button>
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
                        Product No
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
                    {{-- <th scope="col" class="px-6 py-3">
                        Bag. Qty
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Min Lot
                    </th> --}}
                    <th scope="col" class="px-6 py-3">
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
                            -
                        </th>
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
                        {{-- <td class="px-6 py-4">
                            {{ $m->bag_qty }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $m->iss_min_lot }}
                        </td> --}}
                        <td class="px-6 py-4" x-data="{
                            openModalQty(data) {
                                console.log(data[1]);
                                Swal.fire({
                                    title: `Edit qty ${data[2]}`,
                                    input: 'number',
                                    inputValue: data[0],
                                    inputLabel: 'Qty ',
                                    inputPlaceholder: 'qty',
                                    showDenyButton: true,
                                    denyButtonText: `Don't save`
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $dispatch('editQty', {
                                            qty: result.value,
                                            id: data[1]
                                        })
                                        Swal.fire({
                                            timer: 1000,
                                            title: 'Qty changed successfully',
                                            icon: 'success',
                                            showConfirmButton: false,
                                            timerProgressBar: true,
                                        });
                                    } else if (result.isDenied) {
                                        return Swal.fire({
                                            timer: 1000,
                                            title: 'Changes are not saved',
                                            icon: 'info',
                                            showConfirmButton: false,
                                            timerProgressBar: true,
                                        });
                                    }
                                });
                            }
                        }">
                            <button class="btn bg-yellow-500 shadow-md text-white p-2 rounded-lg text-xs"
                                @click="openModalQty(@js([$m->request_qty, $m->id, $m->material_no]))">Edit</button>
                            <button class="btn bg-red-500 shadow-md text-white p-2 rounded-lg text-xs"
                                wire:click="deleteItem('{{ $m->id }}')">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="flex justify-end gap-3">
        <button class="btn bg-red-500 shadow-md text-white p-2 rounded-lg" wire:click="cancelRequest">Cancel
            Request</button>
        <button class="btn bg-blue-500 shadow-md text-white p-2 rounded-lg" wire:click="submitRequest">Submit
            Request</button>
    </div>

    <div wire:loading.flex
        class=" fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        wire:target="saveRequest" aria-label="Loading..." role="status">
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
            $('#lineselect').select2({
                width: 'resolve',
                tags: true
            });
            $('#lineselect').on('select2:select', function(e) {
                @this.set('line_c', e.params.data.id)
            });
        </script>
    @endscript
</div>
