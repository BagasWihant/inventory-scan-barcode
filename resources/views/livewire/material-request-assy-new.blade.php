<div class="max-w-7xl m-auto" x-data="tableManager()" x-init="$wire.on('loadMaterial', (data) => {
    materials = data[0].map(m => ({
        ...m,
        request_qty: m.bom_qty * data[1],
        edited: false
    }));
    qtyOri = data[1];
    qty = data[1];
});">
    <div class="flex gap-3">
        {{-- input --}}
        <div class="w-full">
            {{-- top left --}}
            <div class="flex justify-between gap-2 flex-shrink-0">
                <div class="flex gap-4">
                    <div class="">
                        <label for="issue-date">Tanggal Produksi @if ($filterMode == true)
                                🔒
                            @endif </label>
                        <input id="issue-date" wire:model='date' type="date"
                            @if ($filterMode == true) disabled @endif onfocus="this.showPicker()"
                            class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>

                    <div class="">
                        <label for="issue-date">Line Code @if ($filterMode == true)
                                🔒
                            @endif
                        </label>
                        <select wire:model="line_c" @if ($filterMode == true) disabled @endif
                            class="block w-full p-2 my-1 text-gray-900 border border-gray-300 rounded-lg text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Line Code @if ($filterMode == true)
                                    🔒
                                @endif
                            </option>
                            @foreach ($listLine as $p)
                                <option value="{{ $p->location_cd }}">{{ $p->location_cd }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative">
                        <label for="issue-date">Product Model @if ($filterMode == true)
                                🔒
                            @endif
                        </label>
                        <input wire:model.live.debounce.300ms="productModel" type="text"
                            @if ($filterMode == true) disabled @endif
                            class="block w-full p-2 my-1 mt-1 text-gray-900 border border-gray-300 rounded-lg text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Product Model">

                        @if (strlen($productModel) > 2 && $productModelSearch == true)
                            <ul
                                class="absolute z-10 bg-white border mt-1 max-h-60 overflow-y-auto w-full left-0 right-0">

                                @forelse ($listProductFilter as $option)
                                    <li wire:click="selectProductModel('{{ json_encode($option) }}')"
                                        class="px-3 py-2 cursor-pointer hover:bg-gray-100">
                                        {{ $option->product_no }}
                                    </li>
                                @empty
                                    <li class="px-3 py-2 text-gray-500">Tidak ada hasil.</li>
                                @endforelse
                            </ul>
                        @endif
                    </div>

                    <div class="">
                        <input type="number" name="num" id="nu" x-model.number="qty" @keyup="qtyChange()"
                            placeholder="Qty"
                            class="block w-full p-2 my-1 mt-7 text-gray-900 border border-gray-300 rounded-lg text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                    </div>
                </div>
                <div class="">
                    <button class="btn bg-red-500 shadow-md text-white px-2 py-1 m-1 rounded-lg text-sm"
                        wire:click="resetField">Reset</button>
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
    <div>
        <div class="relative overflow-x-auto shadow-md rounded-lg my-4">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr>
                        <th scope="col" class="px-1 py-3" align="center">
                            Material No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Material Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Bom QTY
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Stock Qty
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Total Qty
                        </th>
                    </tr>
                </thead>
                <template x-for="(material, index) in materials" :key="material.material_no">
                    <tr class="border-b bg-white hover:bg-gray-50">
                        <td class="px-6 py-4" x-text="material.material_no"></td>
                        <td class="px-6 py-4" x-text="material.matl_nm"></td>
                        <td class="px-6 py-4" x-text="material.bom_qty"></td>
                        <td class="px-6 py-4" x-text="material.qty"></td>
                        <td class="px-6 py-4" x-data="{
                            editing: false,
                            inputVal: material.bom_qty * qty,
                            simpanTotalQtyEdit() {
                                const val = parseFloat(this.inputVal);
                                if (val > material.qty) {
                                    this.inputVal = material.bom_qty * qty;
                                    material.request_qty = this.inputVal;
                                    alert('Total Qty melebihi Stock', 'warning');
                                } else {
                                    material.request_qty = val;
                                }
                                material.edited = true;
                                this.editing = false;
                            }
                        }"
                            @click="if (!editing) {
                            editing = true;
                            Alpine.nextTick(() => $refs.input.focus());
                        }">
                            <template x-if="!editing">
                                <span x-text="material.request_qty ?? (material.bom_qty * qty)"
                                    class="cursor-pointer text-blue-600 font-bold">
                                </span>
                            </template>


                            <template x-if="editing">
                                <input type="number" x-model="inputVal" @keydown.enter="simpanTotalQtyEdit()"
                                    x-ref="input" @blur="simpanTotalQtyEdit()" @keydown.esc="editing = false"
                                    class="w-full border border-gray-300 rounded px-2 py-1" />
                            </template>
                        </td>
                    </tr>
                </template>
            </table>
        </div>

        <div class="flex justify-end gap-3">
            @if (!$disableConfirm && $filterMode)
                <button class="btn bg-blue-500 shadow-md text-white p-2 rounded-lg" @click="submitRequest">
                    Submit Request
                </button>
            @endif
        </div>
    </div>

    <div wire:loading.flex
        class="fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        wire:target="submitRequest,selectProductModel" aria-label="Loading..." role="status">
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
    <script>
        function tableManager() {
            return {
                materials: {},
                qtyOri: 0,
                qty: 0,
                reset() {
                    this.materials = {},
                        this.qtyOri = 0,
                        this.qty = 0
                },
                qtyChange() {
                    if (this.qty > this.qtyOri) {
                        this.qty = this.qtyOri;
                        this.materials.forEach(element => {
                            element.request_qty = element.bom_qty * this.qty;
                        })
                        return alert('Input Qty melebihi Qty Plan', 'warning');
                    }
                    this.materials.forEach(element => {
                        if ((element.bom_qty * this.qty) > element.qty) {
                            return alert('Input melebihi Stock', 'warning');
                        }
                        if (element.qty <= 0) {
                            return alert('Stock Kosong', 'warning');
                        }
                        if (!element.edited) {
                            element.request_qty = element.bom_qty * this.qty;
                        }
                    });
                },
                submitRequest() {
                    @this.call('submitRequest', this.materials).then((data) => {
                        if (data.success) {
                            alert(data.message, 'success', false);
                            this.reset();
                        }
                    });
                }
            }
        }

        function alert(title, icon, button = true) {
            Swal.fire({
                title: title,
                icon: icon,
                showConfirmButton: button,
                timerProgressBar: true,
                ...(!button ? {
                    timer: 3000
                } : {})
            });
        }
    </script>
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
            $wire.on('alertB', (event) => {
                Swal.fire({
                    title: event[0].title,
                    icon: event[0].icon,
                    showConfirmButton: true,
                    timerProgressBar: true,
                });
            });
        </script>
    @endscript
</div>
