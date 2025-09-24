<div x-data="{
    msgApi: null,
    apiCalled: false,
    paletInput: null,
    produkBarcode: null,
    paletInputDisable: false,
    listMaterial: [],
    truckingId: null,
    paletInputChange() {
        if (this.paletInput === '' || this.paletInput === null) return;

        const leng = this.paletInput.length
        if (leng > 3) {
            @this.call('paletBarcodeScan', this.paletInput).then((res) => {

                if (res.success && res.material.length > 0) {
                    this.listMaterial = res.material.map((item) => {
                        return {
                            ...item,
                            counter: 0,
                        }
                    })

                    console.log(this.listMaterial);
                    this.truckingId = res.trucking_id
                    this.paletInputDisable = true
                    this.$refs.produkBarcode.focus();

                } else if (res.success && res.material.length < 1) {

                    this.truckingId = res.trucking_id
                    this.msgApi = res.message
                    this.paletInputDisable = true
                    this.$refs.produkBarcode.disabled = true;
                    this.listMaterial = []
                    console.log(this.listMaterial, this.msgApi);
                }

            })
        }
    },
    editQty(data) {
        Swal.fire({
            title: `Edit qty ${data.material_no}`,
            input: 'number',
            inputValue: data.counter,
            inputLabel: 'Qty ',
            inputPlaceholder: 'qty',
            showDenyButton: true,
            denyButtonText: `Don't save`
        }).then((result) => {
            if (result.isConfirmed) {
                data.counter = result.value
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
    resetItem(data) {
        data.counter = 0;
        console.log(data);

        return;
    },
    parseBarcode() {
        if (this.produkBarcode === null) return
        const allowText = new Set(['m', 'c']);
        const pattern = /^\d{2}-\d{4}$/; // kalau mau dipakai nanti
        let lineCode = null;
        let barcode = null;

        const firstText = String(this.paletInput).charAt(0).toLowerCase();
        if (!allowText.has(firstText)) return;

        if (firstText === 'c') {
            // CTI000400ASSY250304XMR1382000830080S32107-3K6-K502   000002
            lineCode = this.produkBarcode.slice(19, 23); // 4 char
            const chunk = this.produkBarcode.slice(23, 36); // 13 char
            barcode = chunk.endsWith('T') ? chunk : chunk.slice(0, 12);
        }

        if (firstText === 'm') {
            // K21759769242166XH872702250001
            lineCode = this.produkBarcode.slice(15, 19); // 4 char
            barcode = this.produkBarcode.slice(7, 15); // 8 char
        }


        const column = pattern.test(String(this.paletInput)) ?
            'serial_no' :
            (allowText.has(firstText) ? 'material_no' : null);
        if (column === null) {
            this.produkBarcode = null;
            this.$refs.produkBarcode.focus();
            return Swal.fire({
                timer: 1000,
                title: 'Tidak ditemukan',
                icon: 'error',
                showConfirmButton: false,
                timerProgressBar: true,
            });
        }

        this.findAndUpdate(column, barcode, lineCode)
        this.produkBarcode = null
        this.$refs.produkBarcode.focus();

    },
    findAndUpdate(column, barcode, lineCode) {
        const norm = v => String(v ?? '').trim();

        const idx = this.listMaterial.findIndex(r =>
            norm(r[column]) === norm(barcode) &&
            norm(r.line_c) === norm(lineCode)
        );

        this.listMaterial[idx].counter += Number(this.listMaterial[idx].picking_qty);

        console.log({
            'column': column,
            'lineCode': lineCode,
            'arcode': barcode,
            'material': this.listMaterial[idx],
            'idx': idx
        });

        {{-- ini ynag mindah ke atas --}}
        if (idx > 0) {
            const [hit] = this.listMaterial.splice(idx, 1);
            this.listMaterial.unshift(hit);
        }

        console.log('semua material', this.listMaterial);

    },
    resetPage() {
        this.msgApi = null
        this.paletInput = null;
        this.produkBarcode = null;
        this.paletInputDisable = false;
        this.listMaterial = [];
        this.truckingId = null;
    },
    confirm() {
        const sendData = this.listMaterial.map((item) => {
            return {
                ...item,
                material: item.material_no,
            }
        })
        @this.call('confirm1', sendData).then((res) => {
            this.resetPage();
        })
    }


}">

    <div class="flex gap-4">
        <div class="flex flex-col w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Palet BARCODE
            </label>


            <input focus x-ref="paletInput" x-model="paletInput" @keyup.debounce.200ms="paletInputChange" type="text"
                :disabled="paletInputDisable" :class="paletInputDisable ? 'bg-gray-200' : 'bg-white'"
                class=" w-full p-4 text-gray-900 border border-gray-300 rounded-lg  text-base ">

        </div>
        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">TRUCKING ID
            </label>
            <input x-model="truckingId" type="text"
                class="block w-full p-4 text-gray-700 border border-gray-300 rounded-lg bg-gray-200 text-base "
                disabled>
        </div>

        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PRODUK BARCODE
            </label>
            <input x-model="produkBarcode" @keydown.debounce.150ms="parseBarcode" type="text" x-ref="produkBarcode"
                class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base ">
        </div>
    </div>

    <div wire:loading.flex
        class=" fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        wire:target="confirm,paletBarcodeScan" aria-label="Loading..." role="status">
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
    <template x-if="listMaterial.length > 0 && msgApi === null">
        <div>
            <div class="grid grid-cols-2 row gap-4 overflow-x-auto sm:rounded-lg p-3 ">
                <div class="w-full">

                    <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">List Barang </h2>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    <div class="flex items-center">
                                        Material No
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <div class="flex items-center">
                                        Line Code
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <div class="flex items-center">
                                        Pax
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <div class="flex items-center">
                                        Qty pickinglist
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(p,i) in listMaterial" :key="p.material_no + p.serial_no">
                                <tr class=" border rounded dark:border-gray-700 ">
                                    <th scope="row"
                                        class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white h-5"
                                        x-text="p.material_no" />
                                    <th scope="row"
                                        class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                        x-text="p.line_c" />
                                    <th scope="row"
                                        class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                        x-text="p.serial_no === '00000' ? 0 : p.pax" />
                                    <th scope="row"
                                        class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                        x-text="p.serial_no === '00000' ? 0 : p.picking_qty" />
                                </tr>
                            </template>
                        </tbody>
                    </table>

                </div>

                <div class="w-full">
                    <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">Berhasil di Scan</h2>
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col " class="px-6 py-3">
                                    Material No
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <div class="flex items-center">
                                        Qty received
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Location
                                </th>
                                <th scope="col" class="w-4">
                                    keterangan
                                </th>
                                <th scope="col" class="px-3 py-3">
                                    <div class="flex items-center">
                                        Action
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(p,i) in listMaterial" :key="p.material_no + p.serial_no">
                                <tr class=" border rounded dark:border-gray-700"
                                    :class="p.counter == p.picking_qty ? 'bg-green-300 dark:bg-green-500' :
                                        p.counter > p.picking_qty ? 'bg-amber-400' : 'bg-red-300 dark:bg-red-500'">
                                    <th class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white h-5"
                                        x-text="p.material_no" />
                                    <th class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                        x-text="p.counter" />
                                    <th class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                        x-text="p.location_cd" />
                                    <th class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                        x-text="p.counter == p.picking_qty ? 'OK CONFIRM'
                                     : p.counter > p.picking_qty ? 'EXCESS' : 'OUTSTANDING / NOT CLOSE'" />
                                    <th scope="row"
                                        class="p-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">

                                        <div class="" x-show="p.counter > 0">

                                            <button @click="resetItem(p)"
                                                class="relative inline-flex items-center justify-center  overflow-hidden text-sm font-medium text-white rounded-lg p-1 group bg-gradient-to-br from-red-800 to-red-500 group-hover:from-red-900 group-hover:to-red-600">
                                                Reset
                                            </button>
                                            <button @click="editQty(p)"
                                                class="relative inline-flex items-center justify-center  overflow-hidden text-sm font-medium text-white rounded-lg p-1 group bg-gradient-to-br from-yellow-800 to-yellow-500 group-hover:from-yellow-900 group-hover:to-yellow-600">
                                                Edit
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="flex justify-end pt-3">
                <button type="button" @click="resetPage"
                    class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Reset</button>
                <button type="button" @click="confirm"
                    class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
                    <span wire:loading.remove>
                        Download & Konfimasi
                    </span>
                    <div role="status" wire:loading wire:target="confirm">
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
    <template x-if="msgApi !== null">

        <div class="w-full">
            <h2 class="p-5 text-2xl text-center font-extrabold dark:text-white" x-text="msgApi"></h2>

            <div class="text-center">
                <button type="button" @click="resetPage"
                    class=" text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Rescan</button>
            </div>
        </div>
    </template>



</div>
