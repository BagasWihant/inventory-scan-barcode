<div class="max-w-7xl mx-auto " x-init="window.addEventListener('product-model-selected', e => {
    isLoading = false;
    listData = e.detail.data;
    listData.forEach(row => {
        row.bom_qty = Number(row.bom_qty).toFixed(3).replace(/\./g, ',');
    });
    canReset = true;
});" x-data="{
    canReset: false,
    isLoading: false,
    listData: [],
    resetDropdown() {
        $dispatch('reset-product-model');
        this.canReset = false;
        this.listData = [];
    },
    selectedProduct() {
        this.isLoading = true;
        this.canReset = true;
    },
    editQty(data) {
        Swal.fire({
            title: `Edit Qty ${data.material_no}`,
            html: `<div class='flex flex-col'>
                                    <strong>Qty</strong>
                                    <input id='editQty1' class='swal2-input' value='${data.bom_qty}'>
                                </div>`,
            showDenyButton: true,
            denyButtonText: `Don't save`,
            didOpen: () => {
                $('#editQty1').focus()
                $('#editQty1').on('keydown', (e) => {
                    if (e.key == 'Enter') {
                        Swal.clickConfirm();
                    }
                })
            },
            preConfirm: () => {
                return [
                    document.getElementById('editQty1').value,
                ];
            }

        }).then((result) => {
            if (!result.value[0].includes('.') && result.value[0].includes(',')) {
                result.value[0] = result.value[0].replace(',', '.');
            }
            anka = parseFloat(result.value[0]);
            qtyInput = anka.toFixed(3).replace(/\./g, ','); // diubah ke koma 
            
            console.log(result.value[0].replace(',', '.'));
            if (result.isConfirmed) {
                if (qtyInput !== null && qtyInput !== '') {
                    data.bom_qty = qtyInput;
                    data.edited = 'edited';
                }
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
    submitData() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        this.isLoading = true;
        this.listData = this.listData.map(r => ({
            ...r,
            bom_qty_parsed: parseFloat(r.bom_qty.replace(',', '.'))
        }));

        @this.call('submitData', this.listData).then((data) => {
            if (data.success) {
                this.isLoading = false;
                this.canReset = false;
                this.resetDropdown();
                this.listData = [];
                return Swal.fire({
                    timer: 1000,
                    title: 'Data Berhasil Disimpan',
                    icon: 'success',
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            }
        })
    }
}">
    <div class="flex gap-3 pb-6">
        <div class="flex justify-start flex-col flex-1">
            <x-search-dropdown :method="'searchDropdown'" :onSelect="'selectDropdown'" :label="'Product Model'" :resetEvent="'reset-product-model'" :field="'product_no'"
                x-bind:disabled="canReset" @change="selectedProduct()"
                x-bind:class="{ 'bg-gray-100 text-gray-800': canReset, 'bg-white text-black': !canReset }" />
        </div>

        <div class="flex justify-end flex-1 items-center">
            <template x-if="!isLoading">
                <button x-show="canReset" @click="resetDropdown()" class="mt-2 bg-red-500 text-white px-3 py-1 rounded">
                    Reset
                </button>
            </template>
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
        <h3 class="text-lg font-medium mb-3">Preview Data:</h3>
        <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Material No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Material Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bom
                            Qty</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(row, index) in listData" :key="index">
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.product_no" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.dc" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.material_no" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.matl_nm" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900" x-text="row.bom_qty" />
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                <button @click="editQty(row)" class="bg-green-500 text-white px-3 py-1 rounded">
                                    Edit
                                </button>
                            </td>
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
