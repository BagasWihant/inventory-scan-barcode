<div class="max-w-7xl m-auto" x-data="{
    type: '1',
}">
    <div class="flex gap-3">
        {{-- input --}}
        <div class="w-full">
            {{-- top left --}}
            <div class="flex justify-between gap-2 flex-shrink-0">
                <div class="flex gap-4">
                    <div class="">
                        <label for="issue-date">Tanggal Produksi</label>
                        <input id="issue-date" wire:change="dateDebounce" wire:model='date' type="date"
                        @if ($userRequestDisable == true) disabled @endif onfocus="this.showPicker()"
                        class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                    
                    <div class="">
                        <label for="issue-date">Line Code</label>
                        <select wire:model="line_c" wire:change="lineChange"
                            class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Line Code</option>
                            @foreach ($listLine as $p)
                                <option value="{{ $p->line_c }}">{{ $p->line_c }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="">
                        <label for="issue-date">Product Model</label>
                        <select wire:model="productModel" wire:change="productModel"
                            class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Product Model</option>
                            @foreach ($listLine as $p)
                                <option value="{{ $p->line_c }}">{{ $p->line_c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="">
                        <input type="number" name="num" id="nu" wire:model="qty" placeholder="Qty"
                            class="block w-full p-2 my-1 mt-7 text-gray-900 border border-gray-300 rounded-lg text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        
                    </div>
                </div>
                <div class="">
                    @if ($userRequestDisable == true)
                        <button
                            class="btn bg-yellow-500 shadow-md text-white px-2 p-1 m-1 rounded-lg text-xs text-nowrap">Ganti</button>
                    @endif
                    <button class="btn bg-red-500 shadow-md text-white px-2 py-1 m-1 rounded-lg text-sm"
                        wire:click="resetField">Clear</button>
                </div>
            </div>
        </div>
        {{-- <div class="w-2/3 bg-gray-200 rounded-md">
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
        </div>  --}}
    </div>

    {{-- table --}}
    <div wire:key="materials-table"
         x-data="{
            Materials: [],
            editingQtyReq: null,
            editedQty: {},
            starteditingQtyReq(material_no) {
                if ($el.closest('[x-data]').type === '2') {
                    this.editingQtyReq = material_no;
                }
            },
            stopeditingQtyReq(material) {
                const editedQty = this.editedQty[material];
                if (editedQty !== undefined) {
                    const selected = this.Materials.find(m => m.material_no === material);
                    if (selected) {
                        selected.request_qty = editedQty;
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
                const requestData = this.Materials.map(item => {
                    return {
                        material_no: item.material_no,
                        material_name: item.material_name,
                        request_qty: this.editedQty[item.material_no] ?? item.request_qty
                    };
                });
                console.log('Sending:', requestData);
                $wire.submitRequest(requestData);
            },
            refreshMaterials() {
                $wire.$refresh();
            }
        }"
        x-init="
            // Initialize materials from the Livewire component
            {{-- $wire.getMaterialData().then(data => {
                Materials = data;
                console.log('Materials loaded:', Materials);
            }); --}}
            
            // Listen for material updates from Livewire
            $wire.on('materialsUpdated', (data) => {
                Materials = data;
                console.log('Materials updated:', Materials);
            });
        "
    >
        <div class="relative overflow-x-auto shadow-md rounded-lg my-4">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr>
                        <th scope="col" class="px-1 py-3" align="center">
                            Transaksi No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Qty
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Bag Qty
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Total Qty
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="Materials.length === 0">
                        <tr class="border-b bg-white">
                            <td colspan="6" class="px-6 py-4 text-center">
                                No materials available. Please select a Line Code.
                            </td>
                        </tr>
                    </template>
                    
                    <template x-for="(material, index) in Materials[0]" :key="material.material_no">
                        <tr class="border-b bg-white hover:bg-gray-50">
                            <td class="px-6 py-4" x-text="index + 1"></td>
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                -
                            </th>
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white" 
                                x-text="material.material_no"></th>
                            <td class="px-6 py-4" x-text="material.material_name"></td>
                            <td class="px-6 py-4" x-text="material.qty_stock"></td>
                            <td class="px-6 py-4 cursor-pointer"
                                @click="starteditingQtyReq(material.material_no)"
                                :class="{ 'cursor-not-allowed': $el.closest('[x-data]').type === '1' }">

                                <!-- Jika type 2 dan sedang edit, tampilkan input -->
                                <template x-if="editingQtyReq === material.material_no && $el.closest('[x-data]').type === '2'">
                                    <input type="number" min="1"
                                        :value="getRequestQty(material.material_no, material.request_qty)"
                                        @input="updateQty(material.material_no, $event.target.value)"
                                        @blur="stopeditingQtyReq(material.material_no)"
                                        class="border border-gray-300 rounded px-2 py-1 w-20">
                                </template>

                                <!-- Jika type 1, hanya tampilkan text -->
                                <template x-if="$el.closest('[x-data]').type === '1'">
                                    <span x-text="getRequestQty(material.material_no, material.request_qty)"></span>
                                </template>

                                <!-- Jika type 2 dan tidak sedang edit, tampilkan text -->
                                <template x-if="editingQtyReq !== material.material_no && $el.closest('[x-data]').type === '2'">
                                    <span x-text="getRequestQty(material.material_no, material.request_qty)"></span>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
        <!-- Debug section, dapat dihapus di production -->
        <div class="bg-gray-100 p-2 mb-4 rounded text-xs overflow-auto max-h-32 hidden">
            <pre x-text="JSON.stringify(Materials, null, 2)"></pre>
        </div>
        
        <div class="flex justify-end gap-3">
            <button class="btn bg-red-500 shadow-md text-white p-2 rounded-lg" wire:click="cancelRequest">
                Cancel Request
            </button>
            <button class="btn bg-blue-500 shadow-md text-white p-2 rounded-lg" @click="submitRequest">
                Submit Request
            </button>
        </div>
    </div>

    <div wire:loading.flex
        class="fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        wire:target="submitRequest, saveRequest" aria-label="Loading..." role="status">
        <svg class="h-20 w-20 animate-spin stroke-white" viewBox="0 0 256 256">
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