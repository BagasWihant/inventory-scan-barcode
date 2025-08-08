<div x-data="{
    materialNo: null,
    lineCode: null,
    date: null,
    dateReceiving: null,
    dateSupply: null,
    listMaterials:{},
    isLoading: false,
    isFiltered: false,
    async filterAction() {
        this.isLoading = true;
        if(this.materialNo == null){
            this.isLoading = false;
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Please fill at least Material No',
                showConfirmButton: false,
                timer: 2500
            })
            return;
        }
        this.isFiltered= true;
        const res = await @this.call('getData',this.materialNo, this.lineCode, this.dateReceiving, this.dateSupply);
        this.listMaterials = res;
        this.isLoading = false;
    },
    resetAction() {
        this.isFiltered= false,
        this.materialNo = null;
        this.lineCode = null;
        this.dateReceiving = null;
        this.listMaterials = [];
    }
}">
    <div class="grid md:grid-cols-4 gap-3 max-w-7xl mx-auto pb-6">
        <div>
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Material No
            </label>
            <input id="materialNo" type="text" x-model="materialNo"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Material No">
        </div>
        <div>
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Line Code
            </label>
            <input id="lineCode" type="text" x-model="lineCode"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Line Code">
        </div>
        <div>
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date Receiving
            </label>
            <input id="dateReceiving" type="date" onclick="this.showPicker()" x-model="dateReceiving"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Select date start">
        </div>
        <div>
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date Supply
            </label>
            <input id="dateSupply" type="date" onclick="this.showPicker()" x-model="dateSupply"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Select date start">
        </div>
    </div>

    <div class="flex max-w-7xl mx-auto pb-5">
        <div class="flex justify-start flex-1" x-show="isFiltered && !isLoading">
            <button @click="$wire.export()"
                :disabled="isLoading"
                class="bg-green-500 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                Export Excel
            </button>
        </div>

        <div class="flex justify-end flex-1">
            <template x-if="!isLoading">
                <button
                    @click="isFiltered ? resetAction() : filterAction()"
                    x-text="isFiltered ? 'Reset' : 'Filter'"
                    :class="isFiltered
                ? 'bg-gradient-to-br from-red-600 to-yellow-700'
                : 'bg-gradient-to-br from-cyan-600 to-blue-700'"
                    class="uppercase text-white px-4 py-2 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
                </button>
            </template>


            <template x-if="isLoading">
                <button disabled
                    class="uppercase bg-gray-400 text-white px-4 py-2 rounded-lg opacity-60 cursor-wait flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Loading...
                </button>
            </template>
        </div>
    </div>



    <div class="max-w-7xl mx-auto">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <template x-if="listMaterials.length > 0">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Material No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Qty
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(m,i) in listMaterials">
                            <tr class="border-b dark:border-gray-700 font-medium hover:text-white text-black"
                                :class="m.status === 'in' ? 'bg-green-200 hover:bg-green-500 ' : 'bg-red-200 hover:bg-red-500'">
                                <th scope="row" class="px-6 py-4  whitespace-nowrap ">
                                    <span x-text="i+1"></span>
                                </th>
                                <td class="px-6 py-4" x-text="m.material_no"></td>
                                <td class="px-6 py-4" x-text="m.qty"></td>
                                <td class="px-6 py-4" x-text="m.created_at"></td>
                                <td class="px-6 py-4" x-text="m.status == 'in' ? 'IN' : 'OUT'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
        </div>
    </div>
</div>