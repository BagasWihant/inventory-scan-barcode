<div class="max-w-7xl m-auto" x-data="{
    type: null,
    tipeOpsi: null,
    date: new Date().toISOString().split('T')[0],
    line_c: null,
    palet: [],
    lines: [],
    Materials: [],
    tmpMaterials: [],
    editingQtyReq: null,
    editedQty: {},
    starteditingQtyReq(material_no) {
        this.editingQtyReq = material_no;
    },
    stopeditingQtyReq(material) {
        const editedQty = this.editedQty[material];
        if (editedQty !== undefined) {
            const selected = this.Materials.find(m => m.material_no === material);
            const selectedtmp = this.tmpMaterials.find(m => m.material_no === material);
            if (selected) {
                if (selected.request_qty_ori < editedQty) {
                    Swal.fire({
                        timer: 1000,
                        title: `Qty Request tidak boleh lebih besar dari  ${selected.request_qty_ori}`,
                        icon: 'error',
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                    return;
                }
                selected.request_qty_new = editedQty;
                selectedtmp.request_qty_new = editedQty;
            }
        }
        this.editingQtyReq = null;
    },
    updateQty(materialNo, value) {
        this.editedQty[materialNo] = Number(value);
    },
    getRequestQty(materialNo, defaultQty, type) {
        if (type == 1) {
            return defaultQty
        }
        return this.editedQty[materialNo] ?? defaultQty;
    },
    submitRequest() {
        console.log(this.Materials);
        const invalid = this.Materials.find(item => Number(item.qty_stock) < Number(item.request_qty_ori));
        if (invalid) {
            return Swal.fire({
                title: `${invalid.material_no} Qty Request lebih besar Qty Stock`,
                icon: 'error',
                showConfirmButton: true,
            })
        }

        $wire.submitRequest(this.Materials, this.type);
        this.Materials = [];
        this.editedQty = {};
        this.resetField();
    },
    initMaterials(data) {

        this.Materials = data;
        this.tmpMaterials = data;
        this.changeType();
    },
    resetField() {
        this.Materials = [];
        this.type = null;
        $wire.resetField();
        document.getElementById('line_c').value = ''
    },
    changeType() {
        if (this.type == 1) {
            this.Materials = this.tmpMaterials.map(m => ({ ...m }));
        } else if (this.type == 2) {
            this.Materials = this.tmpMaterials.map(material => {
                return {
                    ...material,
                    request_qty: 0
                };
            });
        }
    },
    lineChange() {
        if (this.type == null || this.tipeOpsi == null) {
            Swal.fire({
                timer: 1000,
                title: `Please choose type first`,
                icon: 'error',
                showConfirmButton: false,
                timerProgressBar: true,
            });
            this.resetField();
            return;
        }
        $wire.lineChange(this.line_c, this.date, this.tipeOpsi).then(res => {
            this.palet = res.palet;
        });
    },
    tipeChange() {
        $wire.dateDebounce(this.date, this.tipeOpsi).then(data => {
            this.lines = data;
        });
    },
    changeDate(){
        this.tipeOpsi = null
    }
}" x-init="$wire.getMaterialData().then(data => {
    {{-- Materials = data; --}}
    initMaterials(data);
});

// Listen for material updates from Livewire
$wire.on('materialsUpdated', (data) => {
    initMaterials(data[0]);
});">
    <div class="flex gap-3">
        {{-- input --}}
        <div class=" w-full">

            {{-- top left --}}
            <div class="flex justify-between gap-2 flex-shrink-0">
                <div class="flex gap-4" wire:ignore>
                    <div class="flex items-center px-2 rounded">
                        <input id="bordered-radio-1" type="radio" value="1" x-model="type" @change="changeType()"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="bordered-radio-1"
                            class="w-full py-4 ms-2 text-sm font-medium dark:text-gray-300">Reguler</label>
                    </div>
                    <div class="">
                        <label for="">Issue Date</label>
                        <input x-model='date' type="date" onfocus="this.showPicker()" @change="changeDate"
                            class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    <div class="">
                        <select x-model="tipeOpsi" @change="tipeChange" id="tipe"
                            class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Tipe</option>
                            <option value="cot">COT</option>
                            <option value="mat">Material</option>
                        </select>
                    </div>
                    <div class="">
                        <select x-model="line_c" @change="lineChange" id="line_c"
                            class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Line Code</option>
                            <template x-for="line in lines" :key="line.line_c">
                                <option x-text="line.line_c"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="">

                    @if ($userRequestDisable == true)
                        <button
                            class="btn bg-yellow-500 shadow-md text-white px-2 p-1 m-1 rounded-lg text-xs text-nowrap">Ganti</button>
                    @endif
                    <button class="btn bg-red-500 shadow-md text-white px-2 py-1 m-1 rounded-lg text-sm"
                        @click="resetField">Clear</button>
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
    <div>
        <div class="relative overflow-x-auto shadow-md rounded-lg my-4">
            <template x-if="type == 1">
                <p class="text-base text-red-500 font-bold text-center">Mode Reguler tidak bisa edit qty</p>
            </template>
            <template x-if="type == 2">
                <p class="text-base text-red-500 font-bold text-center">Mode Partial bisa edit qty dengan cara klik qty
                    request</p>
            </template>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr>
                        <th scope="col" class="px-1 py-3" align="center">
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
                        <template x-if="type == 2">
                            <th scope="col" class="px-6 py-3">
                                Qty Receive
                            </th>
                        </template>
                        <th scope="col" class="px-6 py-3">
                            Qty Request
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(m,i) in Materials" :key="i">
                        <tr class="border-b bg-white hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span x-text="i+1"></span>
                            </td>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                -
                            </th>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <span x-text="m.material_no"></span>
                            </th>
                            <td class="px-6 py-4">
                                <span x-text="m.material_name"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span x-text="m.qty_stock"></span>
                            </td>

                            <template x-if="type == 2">
                                <td scope="col" class="px-6 py-3">
                                    <span x-text="m.request_qty_ori"></span>
                                </td>
                            </template>
                            <td class="px-6 py-4 cursor-pointer"
                                @click="type === '2' && starteditingQtyReq(m.material_no)"
                                :class="{ 'cursor-not-allowed': type === '1' }">


                                <!-- Jika type 2 dan sedang edit, tampilkan input -->
                                <template x-if="editingQtyReq === m.material_no && type === '2'">
                                    <input type="number" min="1"
                                        :value="getRequestQty(m.material_no, m.request_qty)"
                                        @input="updateQty(m.material_no, $event.target.value)"
                                        @blur="stopeditingQtyReq(m.material_no)"
                                        class="border border-gray-300 rounded px-2 py-1 w-20">
                                </template>

                                <!-- Jika type 1, hanya tampilkan text -->
                                <template x-if="type === '1'">
                                    <span x-text="getRequestQty(m.material_no, m.request_qty,type)"></span>
                                </template>

                                <!-- Jika type 2 dan tidak sedang edit, tampilkan text -->
                                <template x-if="editingQtyReq !== m.material_no && type === '2'">
                                    <span x-text="getRequestQty(m.material_no, m.request_qty,type)"></span>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

        </div>
        <div class="flex justify-end gap-3">
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
        </script>
    @endscript
</div>
