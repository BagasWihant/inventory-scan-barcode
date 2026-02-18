<div x-data="jsmain()"
    @loading-start.window="loading.page = true;loading.text = $event.detail?.text ?? 'Loading...'"
    @loading-stop.window="loading.page = false">
    <div class="flex gap-4">

        <div class="flex flex-col w-1/4">
            <label for="large-input" class="block text-sm font-medium text-gray-900 dark:text-white">Surat Jalan
            </label>
            <input x-model="sj_model" type="text" :disabled="sj_disable" id="surat_jalan"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 sm:text-sm"
                :class="{ 'bg-gray-100': sj_disable }">

        </div>

        <div class="w-1/3">
            <x-search-dropdown :method="'searchDropdown'" :onSelect="'selectDropdown'" :label="'PO'" :field="'kit_no'" :resetEvent="'reset-po-model'"
                x-bind:disabled="canReset"
                x-bind:class="{ 'bg-gray-100 text-gray-800': canReset, 'bg-white text-black': !canReset }" />
        </div>


        <div class="w-1/2">
            <label for="large-input" class="block text-sm font-medium text-gray-900 dark:text-white">Material No
            </label>
            <input x-ref="materialNo" x-model="material_no" @keyup.debounce.300ms="materialNoScan" type="text"
                id="materialNoScan"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 sm:text-sm">
        </div>
    </div>


    <div x-cloak x-show="loading.page"
        class="flex fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        aria-label="Loading..." role="status">
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
        <span class="text-4xl font-medium text-white" x-text="loading.text" />
    </div>
    <template x-if="canReset">
        <div class="flex pt-3">


            <div class="flex w-full justify-end">
                <button type="button" @click="resetPage"
                    class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                    <span>
                        Reset All
                    </span>
                    <div role="status" wire:loading wire:target="resetPage">
                        <svg aria-hidden="true"
                            class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-gray-600 dark:fill-gray-300"
                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor" />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill" />
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </button>
            </div>
        </div>
    </template>

    <div x-show="scanMaterial.length > 0" x-cloak>
        <div class="flex gap-4 overflow-x-auto sm:rounded-lg p-3">
            <div class="w-[80%] shadow-md rounded-lg overflow-hidden">
                <h2 class="p-1 text-sm bg-gray-800 text-white font-bold uppercase tracking-wider text-center">List
                    Barang (PO)</h2>
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs bg-gray-800 uppercase text-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Material No</th>
                            <th scope="col" class="px-4 py-3">Line C</th>
                            <th scope="col" class="px-4 py-3">QTY Picking List</th>
                            <th scope="col" class="px-4 py-3">In Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(m, i) in pagedList" :key="m.material_no.trim() + m.line_c">
                            <tr class="border-b dark:border-gray-700 h-10 transition-colors duration-200"
                                :class="m.counter == 0 ? 'bg-red-50/30' : 'bg-white dark:bg-gray-800'">
                                <td class="px-4 py-0 font-semibold text-gray-900 dark:text-white"
                                    x-text="m.material_no"></td>
                                <td class="px-4 py-0" x-text="m.line_c"></td>
                                <td class="px-4 py-0 font-bold text-blue-700" x-text="m.picking_qty"></td>
                                <td class="px-4 py-0 font-bold text-green-700" x-text="m.stock_in || 0"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="w-full shadow-md rounded-lg overflow-hidden">
                <h2 class="p-1 text-sm bg-blue-200 text-gray-800 font-bold uppercase tracking-wider text-center">Proses
                    Receiving</h2>
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-800 uppercase bg-blue-200">
                        <tr>
                            <th scope="col" class="px-4 py-3">Material No</th>
                            <th scope="col" class="px-4 py-3">Line C</th>
                            <th scope="col" class="px-4 py-3 text-center">Qty received</th>
                            <th scope="col" class="px-4 py-3">Location</th>
                            <th scope="col" class="w-4">keterangan</th>
                            <th scope="col" class="px-3 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(m, i) in pagedList" :key="'scan-' + m.material_no.trim() + m.line_c">
                            <tr class="border-b dark:border-gray-700 h-10 transition-colors duration-200"
                                :class="m.counter == 0 ? 'bg-red-50/30' : 'bg-green-200/40'">
                                <td class="px-4 py-0 font-medium text-gray-900 dark:text-white"
                                    x-text="m.material_no"></td>
                                <td class="px-4 py-0" x-text="m.line_c"></td>
                                <td class="px-4 py-0 text-center">
                                    <div class="flex items-center justify-center">
                                        <span class="text-base font-black"
                                            :class="{
                                                'text-red-500': m.counter == 0 || parseInt(m.counter + parseInt(m.stock_in||0)) > m.picking_qty,
                                                'text-amber-500': m.counter > 0 && parseInt(m.counter + parseInt(m.stock_in||0)) < m.picking_qty,
                                                'text-green-600': parseInt(m.counter + parseInt(m.stock_in || 0)) == parseInt(m.picking_qty)
                                            }"
                                            x-text="m.counter || 0">
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-0 font-mono text-xs" x-text="m.location_cd || '-'"></td>
                                <td class="px-4 py-0 text-center">
                                    <template x-if="m.counter > m.picking_qty">
                                        <span
                                            class="px-2 py-0.5 rounded bg-red-600 text-white text-[9px] font-bold animate-pulse">OVER</span>
                                    </template>
                                </td>
                                <td class="px-4 py-0 text-center">
                                    <button type="button" @click="showScannedModal(scanMaterial.indexOf(m))"
                                        class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 text-[10px] font-bold transition">
                                        DETAIL
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-between mt-6 px-4 bg-gray-50 py-3 rounded-xl shadow-inner">
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-gray-600 uppercase tracking-widest">
                    Halaman <span class="text-blue-600" x-text="currentPage"></span> / <span
                        x-text="totalPages"></span>
                </span>
                <div class="flex shadow-sm rounded-md">
                    <button @click="prevPage()" :disabled="currentPage === 1"
                        class="px-4 py-2 text-sm font-bold bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 disabled:opacity-30 transition-all">
                        PREV
                    </button>
                    <button @click="nextPage()" :disabled="currentPage === totalPages"
                        class="px-4 py-2 text-sm font-bold bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 disabled:opacity-30 transition-all">
                        NEXT
                    </button>
                </div>
            </div>

            <button type="button" @click="confirm" :disabled="loading.confirm"
                class="relative inline-flex items-center justify-center px-8 py-3 overflow-hidden font-bold text-white transition-all bg-blue-600 rounded-xl group hover:bg-blue-700 shadow-lg shadow-blue-200">
                <span x-show="!loading.confirm" class="tracking-widest">KONFIRMASI DATA</span>
                <span x-show="loading.confirm" class="flex items-center">
                    <svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    PROCESSING...
                </span>
            </button>
        </div>
    </div>


    <div id="static-modal" data-modal-backdrop="static" tabindex="-1" x-show="showModal" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="flex inset-0 backdrop-blur-sm overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 max-h-full">

        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div
                class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden">

                <div class="bg-gray-800 p-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        Detail Scanned: <span class="text-blue-300" x-text="dataModal?.material_no"></span>
                    </h3>
                    <button type="button" @click="closeModal" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l18 18"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="flex gap-4 mb-4">
                        <div class="flex-1 bg-blue-50 p-3 rounded-lg border border-blue-100">
                            <p class="text-xs text-blue-600 font-bold uppercase">Total Qty Received</p>
                            <p class="text-2xl font-black text-blue-800" x-text="dataModal?.counter || 0"></p>
                        </div>
                        <div class="flex-1 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <p class="text-xs text-gray-500 font-bold uppercase">Target Picking</p>
                            <p class="text-2xl font-black text-gray-800" x-text="dataModal?.picking_qty || 0"></p>
                        </div>
                    </div>

                    <div class="relative overflow-hidden shadow-sm border border-gray-200 rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-center w-20">No</th>
                                    <th class="px-4 py-3">Qty Scan</th>
                                    <th class="px-4 py-3">Box Number</th>
                                    <th class="px-4 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(m, i) in dataModal.scanned" :key="i">
                                    <tr class="h-12 hover:bg-gray-50 transition">
                                        <td class="px-4 py-0 text-center font-bold text-gray-400" x-text="i + 1"></td>
                                        <td class="px-4 py-0 font-bold text-blue-700 text-lg" x-text="m[0]"></td>
                                        <td class="px-4 py-0 font-mono text-gray-600" x-text="m[1]"></td>
                                        <td class="px-4 py-0">
                                            <div class="flex justify-center gap-2">
                                                <button @click="openModalQty(i)"
                                                    class="px-3 py-1.5 bg-amber-100 text-amber-700 rounded-md text-xs font-bold hover:bg-amber-200 transition">
                                                    EDIT
                                                </button>
                                                <button @click="hapusScanned(i)"
                                                    class="px-3 py-1.5 bg-red-100 text-red-700 rounded-md text-xs font-bold hover:bg-red-200 transition">
                                                    HAPUS
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end p-4 bg-gray-50 border-t border-gray-200">
                    <button @click="closeModal" type="button"
                        class="px-6 py-2 text-sm font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 shadow-sm transition">
                        CLOSE
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function jsmain() {
        return {
            loading: {
                page: false,
                confirm: false,
                text: 'Loading...',
            },
            canReset: false,
            sj_model: '',
            sj_disable: false,
            po_model: '',
            po_disable: false,
            lok_disable: false,
            line_code: '',
            material_no: '',
            currentPage: 1,
            perPage: 10,
            scanMaterial: [],
            boxNo: 1,
            hideForm: false,

            get pagedList() {
                let start = (this.currentPage - 1) * this.perPage;
                let end = start + this.perPage;
                return this.scanMaterial.slice(start, end);
            },

            get totalPages() {
                return Math.ceil(this.scanMaterial.length / this.perPage);
            },

            init() {
                window.addEventListener('po-selected', e => {
                    if (this.sj_model === '') {
                        this.$dispatch('reset-po-model');
                        this.resetPage()
                        return this.showAlert('Silahkan isi Surat Jalan terlebih dahulu');
                    }
                    this.po_model = e.detail.po;
                    this.canReset = true;
                    this.sj_disable = true;
                });

                window.addEventListener('confirmation', e => {
                    this.resetPage();
                });

                window.addEventListener('init-material-data', e => {
                    this.scanMaterial = e.detail.data.map(item => ({
                        ...item,
                        counter: item.counter || 0,
                        scanned: item.scanned || [],
                        location_cd: "ASSY"
                    }));
                    this.currentPage = 1;
                });
            },

            nextPage() {
                if (this.currentPage < this.totalPages) this.currentPage++;
            },
            prevPage() {
                if (this.currentPage > 1) this.currentPage--;
            },

            showAlert(message, timer = null, icon = 'warning', title = 'Perhatian') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: icon,
                    timer: timer,
                    showConfirmButton: timer ? false : true,
                    timerProgressBar: timer ? true : false,
                });
            },

            play() {
                notif = new Audio('{{ asset('assets/sound.wav') }}');
                notif.currentTime = 0; // biar bisa diputar ulang cepat
                notif.play();
            },
            scanToTop(parsed) {
                const key = (parsed.material_no || '').trim();

                // cari index nya
                const idx = this.scanMaterial.findIndex(item =>
                    (item.material_no || '').trim() === key ||
                    (item.supplier_code || '').includes(key)
                );
                if (idx > 0) {
                    const [hit] = this.scanMaterial.splice(idx, 1);
                    this.scanMaterial.unshift(hit);
                }
            },
            parseQR(parseQr) {
                const s = (parseQr ?? '').replace(/\s+/g, '');
                if (!s) {
                    this.material_no = '';
                    this.showAlert('QR kosong atau tidak valid');
                    return null;
                }

                if (!s.toLowerCase().startsWith('pcl')) {
                    this.material_no = '';
                    this.showAlert('QR tidak didukung');
                    return null;
                }


                const split = s.split('/');
                if (split.length < 2) {
                    this.material_no = '';
                    this.showAlert('Material number tidak ditemukan');
                    return null;
                }

                const part1 = split[1] || '';
                // qty = 4 char setelah index 1
                const qtyParse = part1.length >= 5 ? part1.substring(1, 5) : '';

                const hrfBkg = part1.slice(-9); // 9 char terakhir
                const hrfDpn = part1.slice(0, 5); // 5 char depan
                const hapusdepan = part1.replace(hrfDpn, '');
                const material_noParse = hapusdepan.replace(hrfBkg, '');

                const lineParse = (split[2] || '').trim();

                return {
                    material_no: material_noParse,
                    qty: qtyParse,
                    line: lineParse
                };
                // }
            },

            updateItemMaterial(parsed) {
                let mat_no = parsed.material_no.trim(); // Hilangkan spasi
                let line = parsed.line.trim();

                let index = this.scanMaterial.findIndex(s =>
                    s.material_no.trim() === mat_no &&
                    s.line_c.trim() === line
                );
                
                if (index !== -1) {
                    let item = this.scanMaterial[index];

                    item.counter = Number(item.counter || 0) + Number(parsed.qty);

                    if (!item.scanned) item.scanned = [];
                    item.scanned.push([Number(parsed.qty), this.boxNo]);

                    this.scanMaterial.splice(index, 1);
                    this.scanMaterial.unshift(item);

                    this.scanMaterial = [...this.scanMaterial];

                    this.currentPage = 1;

                    if (typeof notif !== 'undefined') notif.play();
                } else {
                    this.showAlert('Material dan line code tersebut tidak ditemukan!', 2000, 'error', 'Gagal');
                }
            },

            boxArray() {
                return [...new Set(this.s.scanned.map(item => item[1]))];
            },

            materialNoScan() {
                if (this.material_no === '') return;
                this.play();

                let parsed = this.parseQR(this.material_no);
                if (!parsed) {
                    return;
                }

                this.loading.page = true;
                this.loading.text = 'Mendapatkan Material...';
                parsed.qr = this.material_no;
                this.line_code = parsed.line;
                this.material_no = '';
                // mencegah double scan, 
                this.$refs.materialNo.disabled = true;

                if (this.scanMaterial.length > 0) {
                    this.updateItemMaterial(parsed);
                    this.loading.page = false;
                    this.$refs.materialNo.disabled = false;
                    this.$refs.materialNo.focus();

                    return
                }
            },
            resetPage() {
                this.$dispatch('reset-po-model');
                this.sj_model = '';
                this.sj_disable = false;
                this.po_model = '';
                this.po_disable = false;
                this.canReset = false;
                this.lok_disable = false;
                this.hideForm = false;
                this.loading.confirm = false;
                this.resetSebagian();
            },
            resetSebagian() {
                this.line_code = '';
                this.material_no = '';
                this.scanMaterial = [];
                this.boxNo = 1;
            },
            openModalQty(index) {
                Swal.fire({
                    title: `Edit qty ${this.dataModal.material_no}`,
                    input: 'number',
                    inputValue: this.dataModal.scanned[index][0],
                    inputLabel: 'Qty ',
                    inputPlaceholder: 'qty',
                    showDenyButton: true,
                    denyButtonText: `Don't save`
                }).then((result) => {
                    if (result.isConfirmed) {

                        let totalScan = 0;
                        this.dataModal.scanned[index][0] = Number(result.value);
                        this.dataModal.scanned.forEach(item => {
                            totalScan += Number(item[0]);
                        });
                        this.dataModal.counter = totalScan;

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
            },
            resetItem(mat_no, line) {
                this.scanMaterial.forEach(item => {
                    if (item.material_no.trim() === mat_no.trim()) {
                        item.counter = 0;
                        item.scanned = [];
                    }
                });
            },
            buildFlatScanned(dir) {
                const rows = [];
                this.scanMaterial.forEach(it => {
                    (it.scanned || []).forEach(tuple => {
                        rows.push(this.toFlatRow(it, tuple));
                    });
                });
                rows.sort((a, b) => {
                    if (a.boxNo !== b.boxNo) return dir === 'desc' ? b.boxNo - a.boxNo : a.boxNo - b.boxNo;
                    // tie-breaker optional
                    if ((a.material_no || '') < (b.material_no || '')) return -1;
                    if ((a.material_no || '') > (b.material_no || '')) return 1;
                    return 0;
                });
                this.flatScanned = rows;
                return rows;
            },
            toFlatRow(item, scanned) {
                const [qty, boxNo] = scanned;
                return {
                    counter: Number(item.counter ?? 0),
                    material_no: item.material_no,
                    kit_no: item.kit_no,
                    line_c: item.line_c,
                    matl_nm: item.matl_nm,
                    picking_qty: item.picking_qty,
                    setup_qty: item.setup_by,
                    location_cd: item.location_cd,
                    location_cd_ori: item.location_cd_ori,
                    total: Number(item.total ?? 0),
                    qty: Number(qty ?? 0),
                    boxNo: Number(boxNo ?? 0),
                };
            },

            confirm() {
                this.loading.confirm = true;
                const sorted = this.buildFlatScanned('asc');

                @this.call('confirm', {
                    sj: this.sj_model,
                    scanned: this.scanMaterial,
                    sorted: sorted,
                    location: 'ASSY',
                    line_code: this.line_code,
                    po: this.po_model,
                }).then((data) => {
                    this.loading.confirm = false;
                    this.currentPage = 1;

                });
            },

            dataModal: [],
            showModal: false,
            showScannedModalold(index) {
                this.showModal = true;
                this.dataModal = this.scanMaterial[index];
            },

            showScannedModal(indexAsli) {
                this.showModal = true;
                this.selectedMaterialIndex = indexAsli;
                this.dataModal = this.scanMaterial[indexAsli];
            },
            closeModal() {
                this.showModal = false;
                this.dataModal = [];
            },
            hapusScanned(index) {

                this.dataModal.counter = this.dataModal.counter - this.dataModal.scanned[index][0];
                this.dataModal.scanned.splice(index, 1);
            }
        }
    }
</script>

@script
    <script>
        const notif = new Audio("{{ asset('assets/sound.wav') }}")

        $wire.on('alert', (event) => {
            Swal.fire({
                timer: event[0].time,
                title: event[0].title,
                icon: event[0].icon,
                text: event[0].text,
                showConfirmButton: false,
                timerProgressBar: true,
            });
        });
    </script>
@endscript
