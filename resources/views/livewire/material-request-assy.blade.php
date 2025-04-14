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

            {{-- top left --}}
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
    <div x-data="{
        selectedMaterials: [],
        selectMaterial(material) {
            const index = this.selectedMaterials.findIndex(m => m.material_no === material.material_no)
            const editedQty = this.editedQty[material.material_no]
            if (editedQty !== undefined) {
                material.request_qty = editedQty
            }
    
            if (index === -1) {
                this.selectedMaterials.push(material)
            } else {
                this.selectedMaterials.splice(index, 1)
            }
        },
        isSelected(materialNo) {
            return this.selectedMaterials.some(m => m.material_no === materialNo)
        },
        editingQtyReq: null,
        editedQty: {},
        starteditingQtyReq(material_no) {
            this.editingQtyReq = material_no;
        },
        stopeditingQtyReq(material) {
            const editedQty = this.editedQty[material]
    
            if (editedQty !== undefined) {
                // Update qty langsung ke selectedMaterials
                const selected = this.selectedMaterials.find(m => m.material_no === material)
                if (selected) {
                    selected.request_qty = editedQty
                }
            }
    
            this.editingQtyReq = null;
        },
        updateQty(materialNo, value) {
            this.editedQty[materialNo] = Number(value)
        },
        getRequestQty(materialNo, defaultQty) {
            return this.editedQty[materialNo] ?? defaultQty
        },
        submitRequest() {
            $wire.submitRequest(this.selectedMaterials)
        }
    }">
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
                            Qty Stock
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Qty Request
                        </th>
                        <th scope="col" class="px-6 py-3">
                        </th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($materialRequest as $m)
                        <tr :class="isSelected('{{ $m->material_no }}') ? 'bg-green-200 hover:bg-green-300' :
                            'bg-white hover:bg-gray-50 '"
                            class="border-b">
                            <td class="px-6 py-4">
                                <input type="checkbox"
                                    :checked="selectedMaterials.some(m => m.material_no === '{{ $m->material_no }}')"
                                    @click='selectMaterial(@json($m))'
                                    id="checkbox-{{ $loop->iteration }}">
                            </td>
                            <td class="px-6 py-4" @click='selectMaterial(@json($m))'>
                                {{ $loop->iteration }}
                            </td>
                            <th scope="row" @click='selectMaterial(@json($m))'
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                -
                            </th>
                            <th scope="row" @click='selectMaterial(@json($m))'
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $m->material_no }}
                            </th>
                            <td class="px-6 py-4" @click='selectMaterial(@json($m))'>
                                {{ $m->material_name }}
                            </td>
                            <td class="px-6 py-4" @click='selectMaterial(@json($m))'>
                                {{ $m->qty_stock }}
                            </td>
                            <td class="px-6 py-4 cursor-pointer" @click="starteditingQtyReq('{{ $m->material_no }}')">

                                <template
                                    x-if="editingQtyReq === '{{ $m->material_no }}' && isSelected('{{ $m->material_no }}')">
                                    <input type="number" min="1"
                                        :value="getRequestQty('{{ $m->material_no }}', {{ $m->request_qty }})"
                                        @input="updateQty('{{ $m->material_no }}', $event.target.value)"
                                        @blur="stopeditingQtyReq('{{ $m->material_no }}')"
                                        class="border border-gray-300 rounded px-2 py-1 w-20">
                                </template>

                                <template
                                    x-if="editingQtyReq !== '{{ $m->material_no }}' || !isSelected('{{ $m->material_no }}')">
                                    <span
                                        x-text="getRequestQty('{{ $m->material_no }}', {{ $m->request_qty }})"></span>
                                </template>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
        <div class="flex justify-end gap-3" x-show="selectedMaterials.length > 0">
            <button class="btn bg-red-500 shadow-md text-white p-2 rounded-lg" wire:click="cancelRequest">Cancel
                Request</button>
            <button class="btn bg-blue-500 shadow-md text-white p-2 rounded-lg" @click="submitRequest">Submit
                Request</button>
        </div>
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
