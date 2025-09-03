<div x-data="{
    canReset: false,
    productModel: '',
    isLoading: false,
    excelData: [],
    excelHeaders: [],
    uploadStatus: {
        message: '',
        type: 'success' // 'success' atau 'error'
    },
    importExcel() {
        this.$refs.fileInput.click();
    },
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.isLoading = true;
        this.uploadStatus = { message: 'File sedang diproses...', type: 'processing' };

        // Validasi
        const allowedTypes = ['.csv', '.xlsx', '.xls'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(fileExtension)) {
            this.uploadStatus = {
                message: 'Tipe file tidak didukung.',
                type: 'error'
            };
            this.isLoading = false;
            return;
        }


    },
    resetDropdown() {
        $dispatch('reset-product-model');
        this.excelData = [];
        this.excelHeaders = [];
        this.canReset = false;
        this.uploadStatus = {
            message: '',
            type: 'success'
        };
    },
    updateCellValue(rowIndex, cellIndex, newValue) {

        this.excelData[rowIndex][cellIndex] = newValue;
        console.log(this.excelData[rowIndex][cellIndex]);

    },
    submit() {
        this.uploadStatus = { message: 'Menyimpan data ...', type: 'processing' };
        this.isLoading = true;
        @this.call('saveBom', this.excelData).then((data) => {

            this.uploadStatus = { message: 'Berhasil simpan data', type: 'success' };
            this.isLoading = false;
        });
        this.resetDropdown();
    }
}" class="max-w-7xl mx-auto " x-init="window.addEventListener('excel-data-loaded', e => {
    try {
        console.log('Event received:', e.detail);
        if (e.detail && e.detail.headers && e.detail.rows) {
            excelHeaders = e.detail.headers;
            excelData = e.detail.rows;
            console.log('Excel data:', excelData);
            console.log('Excel headers:', excelHeaders);
            uploadStatus = { message: 'Data berhasil dimuat!', type: 'success' };
        } else {
            console.error('Invalid event data structure:', e.detail);
            uploadStatus = { message: 'Error loading data', type: 'error' };
        }
    } catch (error) {
        console.error('Error processing excel data:', error);
        uploadStatus = { message: 'Error processing data: ' + error.message, type: 'error' };
    } finally {
        isLoading = false;
    }
});
window.addEventListener('product-model-selected', e => {
    productModel = e.detail;
    canReset = true;
})">
    <div class="flex gap-3 pb-6">
        <div class="flex justify-start flex-col flex-1">
            <x-search-dropdown :method="'searchDropdown'" :onSelect="'selectDropdown'" :label="'Product Model'" :resetEvent="'reset-product-model'" :field="'product_no'"
                x-bind:disabled="canReset"
                x-bind:class="{ 'bg-gray-100 text-gray-800': canReset, 'bg-white text-black': !canReset }" />
            <button x-show="canReset" @click="resetDropdown()" class="mt-2 bg-red-500 text-white px-3 py-1 rounded">
                Reset
            </button>
        </div>

        <div class="flex justify-end flex-1 items-center">
            <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept=".csv,.xlsx,.xls"
                wire:model="importFile" class="hidden">
            <template x-if="!isLoading">
                <button @click="importExcel()" :disabled="isLoading"
                    class="bg-green-500 text-white px-4 py-2 rounded-lg flex items-center gap-2 h-8">
                    Upload Csv
                </button>
            </template>

            <template x-if="isLoading">
                <button disabled
                    class="uppercase bg-gray-400 text-white px-4 py-2 h-8 rounded-lg opacity-60 cursor-wait flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                    Loading...
                </button>
            </template>
        </div>
    </div>


    <!-- Success/Error messages -->
    <div x-show="uploadStatus.message"
        :class="uploadStatus.type === 'success' ? 'bg-green-100 border-green-200 text-green-800' :
            uploadStatus.type === 'processing' ?
            'bg-blue-100 border-blue-200 text-blue-800' :
            'bg-red-100 border-red-200 text-red-800'"
        class="mt-4 p-3 rounded-lg border flex gap-6">
        <p class="text-sm" x-text="uploadStatus.message"></p>
    </div>

     <div x-show="isLoading" class="flex flex-col items-center justify-center gap-2 mt-10">
        <svg class="animate-spin h-10 w-10 text-black" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
            </path>
        </svg>
        Mohon Tunggu...
    </div>
    <!-- Preview data (jika ada) -->
    <div x-show="excelData && Array.isArray(excelData) && excelData.length > 0" class="mt-6">
        <h3 class="text-lg font-medium mb-3">Preview Data:</h3>
        <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <template x-for="header in excelHeaders" :key="header">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                x-text="header"></th>
                        </template>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" x-show="excelData && excelData.length > 0">
                    <template x-for="(row, index) in excelData" :key="index">
                        <tr>

                            <template x-for="(cell, cellIndex) in row" :key="cellIndex">
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">

                                    <template x-if="cellIndex === 'bom_qty'">
                                        <input type="number" step="0.01" :value="cell"
                                            @input="updateCellValue(index, cellIndex, $event.target.value)"
                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            placeholder="0.00" />
                                    </template>

                                    <template x-if="cellIndex !== 'bom_qty'">
                                        <span x-text="cell"></span>
                                    </template>
                                </td>
                            </template>

                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="flex justify-end mt-4">
            <button @click="submit()" :disabled="isLoading"
                class="bg-blue-500 text-white px-4 py-2 rounded-lg flex items-center gap-2 h-8">
                Submit
            </button>
        </div>
    </div>
</div>
