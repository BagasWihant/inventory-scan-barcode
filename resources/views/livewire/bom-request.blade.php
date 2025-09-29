<div class="max-w-7xl mx-auto " x-init="window.addEventListener('product-model-selected', e => {
    if (lineCode === null || qtyRequest === null || date === null) {
        $dispatch('reset-product-model');
        return showAlert('Lengkapi data terlebih dahulu');
    }
    isLoading = false;
    listData = e.detail.data;

    listData.forEach(row => {
        row.qty_request = Math.ceil(qtyRequest * row.bom_qty)
    });
    console.log(listData);
    inputDisable.pm = true
});" x-data="{
    canReset: false,
    inputDisable: {
        line: false,
        date: false,
        qty: false,
        pm: false
    },
    isLoading: false,
    listData: [],
    lines: @js($line),
    lineCode: null,
    qtyRequest: null,
    date: null,
    selected: new Set(),
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
    rowKey(row) {
        return `${row.product_no}|${row.material_no}`;
    },
    isSelected(row) {
        return this.selected.has(this.rowKey(row));
    },
    get allSelected() {
        return this.listData.length > 0 && this.selected.size === this.listData.length;
    },

    toggleRow(row, checked) {
        const key = this.rowKey(row);
        if (checked) this.selected.add(key);
        else this.selected.delete(key);

        this.selected = new Set(this.selected);
        console.log('toggle', this.selected);

    },
    toggleAll(checked) {
        if (checked) {
            this.listData.forEach(r => this.selected.add(this.rowKey(r)));
        } else {
            this.selected.clear();
        }

        this.selected = new Set(this.selected);
        console.log(this.selected);
    },

    resetInput() {
        $dispatch('reset-product-model');
        $dispatch('reset-line-code');
        this.canReset = false;
        this.listData = [];
        this.lineCode = null;
        this.qtyRequest = null;
        this.date = null;
        this.inputDisable = {
            line: false,
            date: false,
            qty: false,
            pm: false
        }
    },
    selectedProduct() {
        this.inputDisable.line = true;
        this.inputDisable.date = true;
        this.inputDisable.qty = true;
        this.isLoading = true;
        this.canReset = true;
    },
    editQty(data) {
        Swal.fire({
            title: `Edit Qty ${data.material_no}`,
            html: `<div class='flex flex-col'>
                                    <strong>Qty</strong>
                                    <input id='editQty1' class='swal2-input' value='${data.qty_request}'>
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
            qtyInput = Number(result.value[0])
            console.log(qtyInput, result.value)
            if (result.isConfirmed) {
                if (qtyInput !== 0) {
                    data.qty_request = parseInt(qtyInput);
                    return Swal.fire({
                        timer: 1000,
                        title: 'Update Success',
                        icon: 'success',
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                }

                return Swal.fire({
                    timer: 1000,
                    title: 'Qty tidak sesuai',
                    icon: 'info',
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
    {{-- cekInt(v) {
        if (typeof v === 'number') return Number.isInteger(v);
        if (typeof v === 'string') return /^-?\d+$/.test(v.trim());
        return false;
    }, --}}
    submitData() {
        const keys = Array.from(this.selected);
        const picked = this.listData
            .filter(r => keys.includes(this.rowKey(r)));
        console.log('Selected rows:', picked);
        if (picked.length == 0) {
            return this.showAlert('Pilih data terlebih dahulu');
        }

        {{-- const invalid = picked.filter(r => !this.cekInt(r.qty_request));
        if (invalid.length > 0) {
            console.log(invalid);
            return this.showAlert('Qty tidak sesuai, ada yang koma. qty harus bulat');
        } --}}
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        this.isLoading = true;
        @this.call('submitData', { data: picked, lineCode: this.lineCode, qtyRequest: this.qtyRequest, date: this.date }).then((data) => {
            if (data.success) {
                this.isLoading = false;
                this.resetInput();
                return this.showAlert(data.msg, 2000, 'success', 'Berhasil');

            } else {
                return this.showAlert(data.msg);


            }
        })
    },

    init() {
        this.$watch('selected', () => {
            if (this.$refs.master) {
                this.$refs.master.indeterminate =
                    this.selected.size > 0 && !this.allSelected;
            }
        });
    },
}">
    <div class="flex gap-3 pb-6">
        <div class="flex justify-start flex-1 gap-3">

            <div class="flex flex-col w-1/4">
                <label for="large-input" class="block text-sm font-medium text-gray-900 dark:text-white">LineCode
                </label>

                <select id="paletselect" x-model="lineCode" :disabled="inputDisable.line"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 sm:text-sm"
                    :class="{ 'bg-gray-100 text-gray-800': inputDisable.line, 'bg-white text-black': !inputDisable.line }">
                    <option selected style="height: 100px">Choose Line</option>
                    <template x-for="line in lines" :key="line.id">
                        <option x-text="line.location_cd" :value="line.location_cd"></option>
                    </template>
                </select>

            </div>

            <div class="flex flex-col w-1/4">
                <label for="large-input" class="block text-sm font-medium text-gray-900 dark:text-white">Request Qty
                </label>
                <input x-model="qtyRequest" type="number" :disabled="inputDisable.qty" id="line"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 sm:text-sm"
                    :class="{ 'bg-gray-100': inputDisable.qty }">

            </div>
            <div class="flex flex-col w-1/4">
                <label for="large-input" class="block text-sm font-medium text-gray-900 dark:text-white">Date Issue
                </label>

                <input x-model="date" type="date" onfocus="this.showPicker()" :disabled="inputDisable.date"
                    id="date"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 sm:text-sm"
                    :class="{ 'bg-gray-100': inputDisable.date }">

            </div>
            <x-search-dropdown :method="'searchPM'" :onSelect="'selectPM'" :label="'Product Model'" :resetEvent="'reset-product-model'" :field="'product_no'"
                x-bind:disabled="inputDisable.pm" @change="selectedProduct()"
                x-bind:class="{ 'bg-gray-100 text-gray-800': inputDisable.pm, 'bg-white text-black': !inputDisable.pm }" />
        </div>

        <div class="flex justify-end items-center">
            <template x-if="!isLoading">
                <button x-show="canReset" @click="resetInput()" class="mt-2 bg-red-500 text-white px-3 py-1 rounded">
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
                        <th class="px-4 py-2">
                            <input type="checkbox" x-ref="master" :checked="allSelected"
                                @change="toggleAll($event.target.checked)">
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
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(row, index) in listData" :key="rowKey(row)">
                        <tr>
                            <td class="px-4 py-2">
                                <input type="checkbox" :checked="isSelected(row)"
                                    @change="toggleRow(row, $event.target.checked)">
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
        <div class="flex justify-end mt-3">
            <button @click="submitData" class="bg-blue-500 text-white px-3 py-1 rounded">
                Submit
            </button>
        </div>
    </div>
</div>
