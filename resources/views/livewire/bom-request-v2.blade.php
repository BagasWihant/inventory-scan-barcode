<div class="max-w-7xl mx-auto p-4" x-data="{
    listData: [],
    selected: new Map(),
    lines: @entangle('lines'),
    lineCode: '',
    date_start: '',
    date_end: '',
    isLoading: false,
    search: '',
    currentPage: 1,
    perPage: 25,

    init() {
        window.addEventListener('product-model-selected', e => {
        console.log(e)
            this.listData = e.detail.data;
            this.selected.clear();
            this.currentPage = 1;
            this.isLoading = false;
        });
    },

    resetPage(){
        this.date_start = ''
        this.date_end = ''
        this.search = ''
        this.selected = new Map(),
        this.listData = [],
        this.lineCode = '',
    },

    get filteredData() {
        if (!this.search) return this.listData;
        let s = this.search.toLowerCase();
        return this.listData.filter(r => {
            {{-- ben gak eror tolowercase --}}
            const productNo = (r.product_no || '').toLowerCase();
            const materialNo = (r.material_no || '').toLowerCase();
            const materialNm = (r.matl_nm || '').toLowerCase();

            return productNo.includes(s) || 
                materialNo.includes(s) || 
                materialNm.includes(s);
        });
    },

    get pagedData() {
        let start = (this.currentPage - 1) * parseInt(this.perPage);
        return this.filteredData.slice(start, start + parseInt(this.perPage));
    },

    get totalPages() {
        return Math.ceil(this.filteredData.length / this.perPage) || 1;
    },

    rowKey(row) { return `${row.product_no}|${row.material_no}`; },

    toggleRow(row) {
        const key = this.rowKey(row);
        this.selected.has(key) ? this.selected.delete(key) : this.selected.set(key, { ...row });
        this.selected = new Map(this.selected);
    },

    toggleAll(checked) {
        this.pagedData.forEach(row => {
            const key = this.rowKey(row);
            checked ? this.selected.set(key, { ...row }) : this.selected.delete(key);
        });
        this.selected = new Map(this.selected);
    },

    get isAllPageSelected() {
        return this.pagedData.length > 0 && this.pagedData.every(r => this.selected.has(this.rowKey(r)));
    },

    editQty(row) {
        Swal.fire({
            title: 'Edit Qty',
            input: 'number',
            inputValue: row.qty_request,
            showCancelButton: true,
        }).then(res => {
            if (res.isConfirmed) {
                row.qty_request = parseInt(res.value);
                if (this.selected.has(this.rowKey(row))) {
                    this.selected.get(this.rowKey(row)).qty_request = row.qty_request;
                }
                this.selected = new Map(this.selected);
            }
        });
    },

    fetchData() {
        console.log(this.lineCode,this.date_end,this.date_start)
        if (!this.lineCode || !this.date_start || !this.date_end) return;
        this.isLoading = true;
        this.$wire.showData(this.lineCode, this.date_start, this.date_end);
    },

    submit() {
        if (this.selected.size === 0) return Swal.fire('Error', 'Pilih material dulu!', 'error');

        this.isLoading = true;
        this.$wire.submitData({
            data: Array.from(this.selected.values()),
            lineCode: this.lineCode,
            date: this.date
        }).then(res => {
            this.isLoading = false;
            if (res.success) {
                Swal.fire('Berhasil', res.msg, 'success');
                this.listData = [];
                this.selected.clear();
            }
        });
    }
}">
    <div class="flex gap-4 bg-white p-4 rounded-lg shadow-sm mb-6 border">
        <div class="flex-col">
            <label class="block mb-2 text-sm font-medium text-gray-600 dark:text-white">Date Start</label>
            <input type="date" x-model="date_start" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
        </div>
        <div class="flex-col">
            <label class="block mb-2 text-sm font-medium text-gray-600 dark:text-white">Date End</label>
            <input type="date" x-model="date_end" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
        </div>
        <div class="flex-col">
            <label class="block mb-2 text-sm font-medium text-gray-600 dark:text-white">Line Code</label>
            <select x-model="lineCode" class="w-full border-gray-300 rounded-md shadow-sm px-3 py-2">
                <option value="">-- Select Line --</option>
                <template x-for="l in lines" :key="l.id">
                    <option x-text="l.location_cd" :value="l.id"></option>

                </template>
            </select>
        </div>
        <div class="flex items-end ml-auto gap-4">
            <template x-if="listData.length > 0">
                <button @click="resetPage" type="button" class="text-white bg-gradient-to-r from-red-500 to-red-600  hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
                    Reset
                </button>
            </template>
            <button @click="fetchData" type="button" class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
                Cari
            </button>

        </div>
    </div>

    <div x-show="listData.length > 0" class="bg-white border rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 bg-gray-50 flex justify-between items-center text-sm">
            <div>
                Total Data: <span class="font-bold" x-text="filteredData.length"></span> |
                Selected: <span class="text-blue-600 font-bold" x-text="selected.size"></span>
            </div>
            <div class="flex items-center gap-2">
                <select x-model="perPage" @change="currentPage = 1" class="border rounded px-2 py-1 text-xs">
                    <option value="25">25 Rows</option>
                    <option value="50">50 Rows</option>
                    <option value="100">100 Rows</option>
                </select>
                <div class="flex border rounded overflow-hidden">
                    <button @click="currentPage--" :disabled="currentPage === 1" class="px-3 py-1 bg-white disabled:bg-gray-100 disabled:text-gray-400 border-r">Prev</button>
                    <div class="px-4 py-1 bg-white flex items-center gap-2 font-medium">
                        Page <span x-text="currentPage" class="mx-1 font-bold"></span> of <span x-text="totalPages" class="mx-1 font-bold"></span>
                    </div>
                    <button @click="currentPage++" :disabled="currentPage === totalPages" class="px-3 py-1 bg-white disabled:bg-gray-100 disabled:text-gray-400 border-l">Next</button>
                </div>
            </div>
        </div>

        <div class="flex border-b bg-gray-50 p-4">
            <input type="text" x-model="search" placeholder="Cari Product No atau Material Name / No..." class="w-full border-gray-300 rounded-md shadow-sm px-3 py-2">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold border-b">
                    <tr>
                        <th class="px-4 py-2 text-center w-10">
                            <input type="checkbox" :checked="isAllPageSelected" @change="toggleAll($event.target.checked)" class="w-4 h-4 text-blue-600 rounded">
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Material No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Material Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bom Qty</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty
                            Request</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <template x-for="row in pagedData" :key="rowKey(row)">
                        <tr :class="selected.has(rowKey(row)) ? 'bg-blue-50' : 'hover:bg-gray-50'">
                            <td class="px-3 py-1 text-center">
                                <input type="checkbox" :checked="selected.has(rowKey(row))" @change="toggleRow(row)" class="w-4 h-4 text-blue-600 rounded">
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.product_no" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.dc" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.material_no" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.matl_nm" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.bom_qty" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.qty_request" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                <button @click="editQty(row)" class="bg-green-500 text-white px-3 py-1 rounded">
                                    Edit Qty
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="isLoading" class="fixed inset-0 bg-black/20 flex items-center justify-center z-50">
        <div class="bg-white p-5 rounded-lg shadow-xl flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="font-medium">Processing Data...</span>
        </div>
    </div>

    <div x-show="selected.size > 0" class="flex my-3">
        <button @click="submit()" class="bg-blue-600 text-white px-4 py-3 rounded-xl gap-2 shadow-2xl hover:bg-blue-700 font-bold flex items-end ml-auto transition-all hover:scale-105">
            SUBMIT <span x-text="selected.size"></span> MATERIALS
        </button>
    </div>
</div>
