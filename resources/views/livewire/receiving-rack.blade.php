<div x-init="window.addEventListener('data-load', e => {
    isLoading = false;
    listData = e.detail.data;
    if (listData.length > 0) {
        inputDisable.palet = true;
        listData.forEach(row => {
            row.scanned_pack = 0
        });
        $refs.materialNo.focus();
    }
    console.log(listData);
})" x-data="{
    isLoading: false,
    listData: [],
    paletInputDisable: false,
    inputDisable: {
        palet: false,
    },
    materialNo: '',
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
    selectedPalet() {
        this.isLoading = true;
    },
    rowKey(i, row) {
        return `${i}|${row.material_no}`;
    },
    materialScan() {
        const idx = this.listData.findIndex(r =>
            r.material_no.replace(/ /g, '') === this.materialNo.replace(/ /g, '') ||
            r.material_no === this.materialNo);
        if (idx > -1) {
            this.listData[idx].scanned_pack += 1;
        }
        this.materialNo = '';
    },
    submitData() {
        this.showAlert('Belum ada aksi', 2000, 'warning', 'Perhatian');
    }
}">

    <div class="flex gap-4 flex-col md:flex-row mb-7">
        <div class="flex flex-col w-full">
            <x-search-dropdown :method="'searchPalet'" :onSelect="'selectPalet'" :label="'Palet No'" :resetEvent="'reset-paletno'" :field="'pallet_no'"
                x-bind:disabled="inputDisable.palet" @change="selectedPalet()"
                x-bind:class="{ 'bg-gray-100 text-gray-800': inputDisable.palet, 'bg-white text-black': !inputDisable.palet }" />
        </div>

        <div class="w-full">
            <label for="large-input" class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Material No
            </label>
            <input x-model="materialNo" @keydown.debounce.150ms="materialScan" type="text" x-ref="materialNo"
                class="block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 sm:text-sm ">
        </div>
    </div>

    <div x-show="isLoading" class="flex flex-col items-center justify-center gap-2 mt-7">
        <svg class="animate-spin h-10 w-10 text-black" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
            </path>
        </svg>
        Load Data...
    </div>


    <div x-show="listData.length > 0">
        <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pallet No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Material No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Picking Qty</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pack
                            Stock</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pack
                            Scanned</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(row, index) in listData" :key="rowKey(index, row)">
                        <tr
                            :class="{
                                'bg-green-200': row.scanned_pack === Number(row.pack),
                                'bg-red-400': row.scanned_pack > Number(row.pack),
                                'bg-orange-200': row.scanned_pack < Number(row.pack)
                            }">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.pallet_no" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.material_no" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.picking_qty" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.pack" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.scanned_pack" />
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="flex justify-end mt-3">
            <button @click="submitData" class="bg-blue-500 text-white px-3 py-1 rounded">
                Submit
            </button>
        </div>
    </div>
</div>
