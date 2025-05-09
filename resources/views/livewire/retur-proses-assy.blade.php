<div class="max-w-7xl mx-auto" x-data="{
    todayCount: 0,
    showModal: false,
    data: [],
    dataDetail: [],
    editQty: null,
    editedQtyPassed: {},
    editedQtyFail: {},
    editingQtyPassed: null,
    editingQty: null,
    loadData() {
        @this.call('loadData').then((res) => {
            this.data = res;
        })
    },
    showMaterialDetails(trx) {
        @this.call('getDetail', trx).then((data) => {
            this.showModal = true;
            this.dataDetail = data;
        });
    },
    closeModal() {
        this.showModal = false
        this.dataDetail = []
    },

    saveDetailScanned(status) {
        {{-- let status = null;
        if (this.dataDetail[0].status == '-') {
            status = '0';
        } else if (this.dataDetail[0].status == '0') {
            status = '1';
        } --}}
        @this.call('saveDetailScanned', [this.dataDetail[0].no_retur, status, this.dataDetail]).then((res) => {
            if (res == 'success') {
                Swal.fire({
                    timer: 1000,
                    title: `Retur Berhasil di proses`,
                    icon: 'success',
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
                this.loadData();
            } else {
                Swal.fire({
                    timer: 1000,
                    title: `Retur Gagal di proses`,
                    icon: 'error',
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            }
        });
        this.closeModal();
    },
    getRequestQtyPassed(mat, def) {
        return this.editedQtyPassed[mat] ?? def;
    },
    updateQtyPassed(mat, value) {
        const selected = this.dataDetail.find(m => m.material_no === mat);
        console.log(value, selected.qty);
        if (Number(value) > Number(selected.qty)) {
            Swal.fire({
                timer: 1000,
                title: `Qty melebihi stok. maximal ${selected.qty}`,
                icon: 'error',
                showConfirmButton: false,
                timerProgressBar: true,
            });
            this.editedQtyPassed[mat] = Number(selected.qty);
            this.editedQtyFail[mat] = Number(0);
            const inputElement = document.getElementById('inputQtyPassed-' + mat);
            inputElement.value = selected.qty;
            return;
        }
        let fail = selected.qty - value;
        console.log(value, selected.qty,fail);
        this.editedQtyPassed[mat] = Number(value);
        this.editedQtyFail[mat] = Number(fail);
    },
    stopeditingQtyPassed(material) {
        const editedQty = this.editedQtyPassed[material];
        const editedQtyFail = this.editedQtyFail[material];
        if (editedQty !== undefined) {
            const selected = this.dataDetail.find(m => m.material_no === material);
            if (selected) {
                selected.retur_qty_pass = editedQty;
                selected.retur_qty_fail = editedQtyFail;
            }
        }
        this.editingQty = null;
    },
    getRequestQtyFail(mat, def) {
        return this.editedQtyFail[mat] ?? def;
    },
    editQty(material_no, stat) {
        if (stat === '0' || stat === 0) {
            this.editingQty = 'inputQtyFail-' + material_no;
        } else {
            this.editingQty = 'inputQtyPassed-' + material_no;
        }

        this.$nextTick(() => {
            setTimeout(() => {
                let inputId;
                if (stat === '0' || stat === 0) {
                    inputId = 'inputQtyFail-' + material_no;
                } else {
                    inputId = 'inputQtyPassed-' + material_no;
                }

                // Try to get the element by ID first (more reliable)
                const inputElement = document.getElementById(inputId);
                if (inputElement) {
                    inputElement.focus();
                    inputElement.select();
                } else {
                    const inputRef = this.$refs[inputId];
                    if (inputRef) {
                        inputRef.focus();
                        inputRef.select();
                    }
                }
            }, 100);
        });
    },
    updateQtyFail(mat, value) {
        const selected = this.dataDetail.find(m => m.material_no === mat);
        if (Number(value) > Number(selected.qty)) {
            Swal.fire({
                timer: 1000,
                title: `Qty melebihi stok. maximal ${selected.qty}`,
                icon: 'error',
                showConfirmButton: false,
                timerProgressBar: true,
            });
            this.editedQtyPassed[mat] = Number(0);
            this.editedQtyFail[mat] = Number(selected.qty);
            const inputElement = document.getElementById('inputQtyFail-' + mat);
            inputElement.value = selected.qty;
            return;
        }
        let pass = selected.qty - value;
        this.editedQtyPassed[mat] = Number(pass);
        this.editedQtyFail[mat] = Number(value);
    },
    stopeditingQtyFail(material) {
        const editedQty = this.editedQtyFail[material];
        const editedQtyPass = this.editedQtyPassed[material];
        if (editedQty !== undefined) {
            const selected = this.dataDetail.find(m => m.material_no === material);
            if (selected) {
                selected.retur_qty_fail = editedQty;
                selected.retur_qty_pass = editedQtyPass;
            }
        }
        this.editingQty = null;
    }
}" x-init="loadData();">
    <div class="text-2xl text-center font-extrabold py-6" id="setHerePagination">Proses Retur Assy</div>

    <div class="flex justify-end"><span>Total Transaksi Hari ini : <b x-text="todayCount"></b></span></div>
    <div class="relative w-full">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
        </div>
        <input type="text" id="search"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Transaksi No." />
    </div>
    <div>
        <div class="relative overflow-x-auto shadow-md rounded-lg my-4">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-gray-900 uppercase bg-gray-300">
                    <tr>
                        <th scope="col" class="px-1 py-3">
                            No Retur
                        </th>
                        <th scope="col" class="px-1 py-3">
                            Issue Date
                        </th>
                        <th scope="col" class="px-1 py-3">
                            Line Code
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(d,i) in data" :key="i">
                        <tr :class="{
                            'bg-red-700 text-white font-semibold hover:bg-red-800': d.status === 'x',
                            'bg-red-400 text-white font-semibold hover:bg-red-600': d.status === '-',
                            'bg-yellow-400 text-white font-semibold hover:bg-yellow-600': d.status === '0',
                            'bg-green-700 text-white font-semibold hover:bg-green-800': d.status === '1'
                        }"
                            class="py-2 h-10">
                            <td class="px-2" role="button" @click="showMaterialDetails(d.no_retur)">
                                <span x-text="d.no_retur"></span>
                            </td>
                            <td role="button" @click="showMaterialDetails(d.no_retur)">
                                <span x-text="d.issue_date"></span>
                            </td>
                            <td role="button" @click="showMaterialDetails(d.no_retur)">
                                <span x-text="d.line_c"></span>
                            </td>
                            <td role="button" @click="showMaterialDetails(d.no_retur)">
                                <span
                                    x-text="d.status == '-'  ? 'Belum diproses' : d.status == 'x' ? 'DiReject' : d.status == '1' ? 'Sudah diproses' : '??'"></span>
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>

            <!-- Main modal -->
            <div id="static-modal" data-modal-backdrop="static" tabindex="-1" x-show="showModal" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="scale-90 backdrop-blur-sm"
                x-transition:enter-end=" scale-100 backdrop-blur-md"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start=" scale-100"
                x-transition:leave-end="scale-90"
                class="flex inset-0 sc backdrop-blur-md overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 max-h-full">
                <div class="relative p-4 w-full max-w-7xl max-h-full">
                    <!-- Modal content -->
                    <template x-if="dataDetail.length >0 ">
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div class="sticky top-0 z-40 bg-white">
                                <div
                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 ">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white"
                                        x-text="dataDetail[0].no_retur">Detail</h3>
                                    <button type="button" @click="closeModal"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                            </div>
                            <div class="text-center">
                                <span class="text-red-600">Klik qty untuk edit </span>
                            </div>
                            <div class="p-3">
                                <div class="relative overflow-y-auto shadow-md rounded-lg my-4">
                                    <table
                                        class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead class="text-gray-900 uppercase bg-gray-300">
                                            <tr>
                                                <th scope="col" class="px-3 py-3">
                                                    Material No
                                                </th>
                                                <th scope="col" class="px-3 py-3">
                                                    Material Name
                                                </th>
                                                <th scope="col" class="px-3 py-3">
                                                    Line C
                                                </th>
                                                <th scope="col" class="px-3 py-3">
                                                    Qty Retur Request
                                                </th>
                                                <th scope="col" class="px-3 py-3">
                                                    Qty Retur Fail
                                                </th>
                                                <th scope="col" class="px-3 py-3">
                                                    Qty Retur Passed
                                                </th>
                                                <th>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="d in dataDetail">
                                                <tr>
                                                    <td>
                                                        <span x-text="d.material_no"></span>
                                                    </td>
                                                    <td>
                                                        <span x-text="d.material_name"></span>
                                                    </td>
                                                    <td>
                                                        <span x-text="d.line_c"></span>
                                                    </td>
                                                    <td>
                                                        <span x-text="d.qty"></span>
                                                    </td>
                                                    <td class="px-6 py-4 cursor-pointer"
                                                        @click="editQty(d.material_no, '0')">
                                                        <!-- Jika sedang edit -->
                                                        <div x-show="editingQty === 'inputQtyFail-'+d.material_no">
                                                            <input type="number" min="1"
                                                                :id="'inputQtyFail-' + d.material_no"
                                                                :x-ref="'inputQtyFail-' + d.material_no"
                                                                :value="getRequestQtyFail(d.material_no, 0)"
                                                                @input="updateQtyFail(d.material_no, $event.target.value)"
                                                                @blur="stopeditingQtyFail(d.material_no)"
                                                                class="border border-gray-300 rounded px-2 py-1 w-20">
                                                        </div>

                                                        <!-- Jika tidak sedang edit -->
                                                        <template x-if="editingQty !== 'inputQtyFail-'+ d.material_no">
                                                            <span x-text="getRequestQtyFail(d.material_no, 0)"></span>
                                                        </template>
                                                    </td>
                                                    <td class="px-6 py-4 cursor-pointer"
                                                        @click="editQty(d.material_no,1)">

                                                        <!-- Jika sedang edit -->
                                                        <div class=""
                                                            x-show="editingQty === 'inputQtyPassed-'+ d.material_no">

                                                            <input type="number" min="1"
                                                                :id="'inputQtyPassed-' + d.material_no"
                                                                :x-ref="'inputQtyPassed-' + d.material_no"
                                                                :value="getRequestQtyPassed(d.material_no, 0)"
                                                                @input="updateQtyPassed(d.material_no, $event.target.value)"
                                                                @blur="stopeditingQtyPassed(d.material_no)"
                                                                class="border border-gray-300 rounded px-2 py-1 w-20">
                                                        </div>


                                                        <!-- Jika tidak sedang edit -->
                                                        <template
                                                            x-if="editingQty !== 'inputQtyPassed-'+d.material_no">
                                                            <span
                                                                x-text="getRequestQtyPassed(d.material_no, 0)"></span>
                                                        </template>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <div
                                class="flex items-center justify-between p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600 sticky bottom-0 bg-white">
                                <div class="">

                                    <button @click="closeModal" type="button"
                                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Close</button>
                                </div>
                                {{-- 
                                <template x-if="dataDetail.length > 0 && dataDetail[0].status != '1' "> --}}
                                <div class="">

                                    <button type="button" @click="saveDetailScanned('x')"
                                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-50 focus:outline-none bg-red-500 rounded-lg border  hover:bg-red-600 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                        <span x-text="'Reject'"></span>
                                    </button>
                                    <button type="button" @click="saveDetailScanned('1')"
                                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-50 focus:outline-none bg-blue-500 rounded-lg border  hover:bg-blue-600 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                        <span x-text="'Confirm QA'"></span>
                                    </button>
                                </div>
                                {{-- </template> --}}

                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div wire:loading.flex
            class=" fixed z-[99] bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
            wire:target="getDetail" aria-label="Loading..." role="status">
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
            <span class="text-4xl font-medium text-white">Loading...</span>
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
</div>
