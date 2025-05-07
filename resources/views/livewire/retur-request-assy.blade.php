<div class="max-w-7xl m-auto" x-data="{
    Materials: [],
    editingQtyReq: null,
    editedQty: {},
    pollingInterval: null,
    tabelStream: {},

    starteditingQtyReq(material_no) {
        this.editingQtyReq = material_no;
        this.$nextTick(() => {
            const inputRef = this.$refs['inputQty-' + material_no];
            if (inputRef) {
                inputRef.focus();
            }
        });
    },
    stopeditingQtyReq(material) {
        const editedQty = this.editedQty[material];
        if (editedQty !== undefined) {
            const selected = this.Materials.find(m => m.material_no === material);
            if (selected) {
                if (selected.qty_retur < editedQty) {
                    Swal.fire({
                        timer: 1000,
                        title: `Qty Request tidak boleh lebih besar dari  ${selected.qty_retur}`,
                        icon: 'error',
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                    this.editedQty[material] = selected.qty_retur;
                    return;
                }
                selected.retur_qty = editedQty;
            }
        }
        this.editingQtyReq = null;
    },
    updateQty(materialNo, value) {
        this.editedQty[materialNo] = Number(value);
    },
    getRequestQty(materialNo, defaultQty) {
        return this.editedQty[materialNo] ?? defaultQty;
    },
    submitRequest() {
        $wire.submitRequest(this.Materials).then((res) => {
            if (res == 'success') {

                this.Materials = [];
                this.editedQty = {};
                Swal.fire({
                    timer: 1000,
                    title: `Retur Request Berhasil di kirimkan`,
                    icon: 'success',
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            } else {
                Swal.fire({
                    timer: 1000,
                    title: `Retur Request Gagal di kirimkan`,
                    icon: 'error',
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            }
        });
    },
    resetField() {
        this.Materials = [];
        $wire.resetField();
        document.getElementById('line_c').value = ''
    },
    lineChange() {
        $wire.lineChange().then((res) => {
            this.Materials = res;

        });
    },
    pollingRequest() {
        $wire.streamTableSum().then((res) => {
            console.log('load pertama');
            this.tabelStream = res;
        });

        this.pollingInterval = setInterval(() => {
            $wire.streamTableSum().then((res) => {
                this.tabelStream = res;
            });
        }, 3500);
    },
}" x-init="pollingRequest();">

    <div class="flex gap-3">
        {{-- input --}}
        <div class=" w-full">

            {{-- top left --}}
            <div class="flex justify-between gap-2 flex-shrink-0">
                <div class="flex gap-4">
                    <div class="">
                        <label for="">Issue Date</label>
                        <input wire:change="dateDebounce" wire:model='date' type="date" onfocus="this.showPicker()"
                            class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    <div class="">
                        <select wire:model="line_c" @change="lineChange" id="line_c"
                            class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg  text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Line Code</option>
                            @foreach ($listLine as $p)
                                <option value="{{ $p->line_c }}">{{ $p->line_c }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="">

                    <button class="btn bg-red-500 shadow-md text-white px-2 py-1 m-1 rounded-lg "
                        @click="resetField">Clear</button>
                </div>

            </div>

        </div>

        <div class="w-2/3 bg-gray-200 rounded-md">
            <strong class="flex justify-center">Total Qty Retur &nbsp;<span x-text="tabelStream.qty"></span></strong>
            <div class="relative overflow-y-auto shadow-md rounded-lg max-h-40">
                <table class="w-full text-xs text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs sticky top-0 text-gray-700 bg-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                No Retur
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
                        <template x-for="(m,index) in tabelStream.data" :key="index">
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <span x-text="m.no_retur"></span>
                                </th>
                                <td class="px-6 py-4">
                                    <span x-text="m.count"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span x-text="m.time_request"></span>
                                </td>
                            </tr>
                        </template>

                    </tbody>
                </table>
            </div>


        </div>
    </div>


    {{-- table --}}
    <div>
        <div class="relative overflow-x-auto shadow-md rounded-lg my-4">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr>
                        <th scope="col" class="px-1 py-3" align="center">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Material No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Material Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Qty Supply Assy
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Qty Receive Assy
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Qty Retur
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(m,index) in Materials" :key="index">
                        <tr
                            :class="getRequestQty(m.material_no, m.qty_retur) > 0 ? 'bg-green-300 text-black' : 'bg-white'">
                            <td class="px-6 py-4">
                                <span x-text="index + 1"></span>
                            </td>
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <span x-text="m.material_no"></span>
                            </th>
                            <td class="px-6 py-4" wire:ignore.self>
                                <span x-text="m.material_name"></span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 " wire:ignore.self>
                                <span x-text="m.qty_supply_assy"></span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 " wire:ignore.self>
                                <span x-text="m.qty"></span>
                            </td>
                            <td class="px-6 py-4 cursor-pointer" @click="starteditingQtyReq(m.material_no)"
                                wire:ignore.self>

                                <!-- Jika sedang edit -->
                                <template x-if="editingQtyReq === m.material_no">
                                    <input type="number" min="1" :x-ref="'inputQty-' + m.material_no"
                                        :value="getRequestQty(m.material_no, m.qty_retur)"
                                        @input="updateQty(m.material_no, $event.target.value)"
                                        @blur="stopeditingQtyReq(m.material_no)"
                                        class="border border-gray-300 rounded px-2 py-1 w-20">
                                </template>

                                <!-- Jika tidak sedang edit -->
                                <template x-if="editingQtyReq !== m.material_no">
                                    <span x-text="getRequestQty(m.material_no, m.qty_retur)"></span>
                                </template>
                            </td>
                        </tr>
                    </template>



                </tbody>
            </table>

        </div>
        <div class="flex justify-end gap-3">
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
