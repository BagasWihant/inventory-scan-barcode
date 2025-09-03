<div x-data="{
    canReset: false,
    sj_model: '',
    sj_disable: false,
    po_model: '',
    po_disable: false,
    lok_model: '',
    lok_disable: false,
    line_code: '',
    material_no: '',
    mcs: false,
    listMaterial: [],
    scanMaterial: [],
    // fungsi utilitas
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
    parseQR(parseQr) {
        const s = (parseQr ?? '').replace(/\s+/g, '');
        if (!s) {
            this.material_no = '';
            this.showAlert('QR kosong atau tidak valid');
            return null;
        }

        if (s.includes('//')) {
            // === MCS ===
            const split = s.split('//');
            if (split.length < 2) {
                this.material_no = '';
                this.showAlert('QR Code tidak valid');
                return null;
            }

            const split1 = (split[0] || '').split('-');
            const material_noParse = split1[0] || '';
            const qtyParse = (split1[2] || '').replace(/\D/g, ''); // ambil angka saja
            const lineParse = '';

            return { material_no: material_noParse, qty: qtyParse, line: lineParse };
        } else {
            // === Format lain: a/b/c ===
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

            if (this.line_code && this.line_code !== lineParse) {
                this.material_no = '';
                this.showAlert('PO atau Linecode berbeda');
                return null;
            }

            return { material_no: material_noParse, qty: qtyParse, line: lineParse };
        }
    },
    scanMaterialAct(parsed) {
        mat_no = parsed.material_no
        this.scanMaterial.forEach(item => {
            if (item.material_no.trim() === mat_no) {
                item.counter = Number(item.counter) + Number(parsed.qty);
            }
        });
        console.log(this.scanMaterial,this.listMaterial);
    },
    // end util

    materialNoScan() {
        if (this.material_no === '') return;
        this.play();
        if (this.lok_model === '') {
            return this.showAlert('Silahkan isi lokasi terlebih dahulu');
        }

        let parsed = this.parseQR(this.material_no);
        console.log(parsed);
        if (!parsed) {
            this.showAlert('QR tidak valid');
            return;
        }
        parsed.qr = this.material_no;
        this.line_code = parsed.line;
        this.material_no = '';

        if (this.scanMaterial.length > 0) {
            console.log('update qty')
            this.scanMaterialAct(parsed);
            return
        }

        @this.call('scanMaterial', parsed).then((data) => {
            this.listMaterial = data;
            this.scanMaterial = data;
            console.log(data);
        });
    },
    poChange() {
        console.log(this.po_model);
    },
    resetPage() {
        $dispatch('reset-po-model');
        this.sj_model = '';
        this.sj_disable = false;
        this.po_model = '';
        this.po_disable = false;
        this.lok_model = '';
        this.lok_disable = false;
        this.line_code = '';
        this.material_no = '';
        this.mcs = false;
        this.canReset = false;
    }
}" x-init="window.addEventListener('po-selected', e => {
    if (sj_model === '') {
        $dispatch('reset-po-model');
        return showAlert('Silahkan isi Surat Jalan terlebih dahulu');
    }
    mcs = true;
    po_model = e.detail.po;
    canReset = true;
    sj_disable = true;
})">
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

        {{-- <div class="w-1/4">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white ">Setup By
            </label>
            <input wire:model="input_setup_by" disabled
                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-100 text-base">
        </div> --}}
        <template x-if="mcs">
            <div class="flex gap-4 w-1/2">
                <div class="w-1/2">
                    <span for="large-input" class="block text-sm font-medium text-gray-900 dark:text-white ">Location
                    </span>
                    <select class="w-full px-3 py-2  text-gray-900 border border-gray-300 rounded-lg  text-base"
                        name="location" x-model="lok_model" :disabled="lok_disable"
                        :class="{ 'bg-gray-100': lok_disable }"
                        @change="
                            if(lok_model !== '-') {
                                lok_disable = true;
                                $nextTick(() => { $refs.materialNo.focus() });
                            }
                        ">
                        <option value="-">Location</option>
                        <option value="ASSY">ASSY</option>
                        <option value="CNC">CNC</option>
                    </select>
                </div>
                <div class="w-1/2">
                    <span for="large-input" class="block text-sm font-medium text-gray-900 dark:text-white ">Line
                        Code
                    </span>
                    <input x-model="line_code" disabled {{-- class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-100 text-base" --}}
                        class="mt-1 block w-full border border-gray-300 bg-gray-100 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 sm:text-sm">
                </div>
            </div>
        </template>

        <div class="w-1/2">
            <label for="large-input" class="block text-sm font-medium text-gray-900 dark:text-white">Material No
            </label>
            <input x-ref="materialNo" x-model="material_no" @keyup.debounce.300ms="materialNoScan" type="text"
                id="materialNoScan"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 sm:text-sm">
        </div>
    </div>
    <div wire:loading.flex
        class=" fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        wire:target="materialNoScan,resetPage,confirm,choosePo" aria-label="Loading..." role="status">
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
        <span class="text-4xl font-medium text-white">{{ $statusLoading ?? 'Loading...' }}</span>
    </div>
    <template x-if="canReset">
        <div class="flex justify-end pt-3">
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
    </template>


    <div x-show="listMaterial.length > 0">


        <div class="flex gap-4 overflow-x-auto sm:rounded-lg p-3 ">
            <div class="w-[90%]">

                <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">List Barang </h2>
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Material No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Line C
                            </th>
                            <th scope="col" class="px-6 py-3">
                                QTY Picking List
                            </th>
                            <th scope="col" class="px-6 py-3">
                                In Stock
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(m,i) in listMaterial" :key="i">
                            <tr>
                                <th class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    x-text="m.material_no" />
                                <th class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    x-text="m.line_c" />
                                <th class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    x-text="m.picking_qty" />
                                <th class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                    x-text="m.stock_in" />

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
                            <th scope="col " class="px-6 py-3">
                                Line C
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
                        <template x-for="(s,i) in scanMaterial " :key="i">
                            <tr
                                :class="s.total == s.counter && s.qty_more == 0 ?
                                    'bg-green-300 dark:bg-green-500' :
                                    (s.counter > s.total || s.qty_more > 0 ?
                                        'bg-amber-400' :
                                        'bg-red-300 dark:bg-red-500')">
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <span x-text="s.material_no"></span>
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <span x-text="s.line_c"></span>
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <span x-text="s.counter"></span>
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <span x-text="s.location"></span>
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <span
                                        x-text="
                                            s.total == s.counter && s.qty_more == 0
                                                ? 'OK CONFIRM'
                                                : (s.counter > s.total || s.qty_more > 0
                                                    ? 'EXCESS'
                                                    : 'OUTSTANDING / NOT CLOSE')
                                        " />
                                </th>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="flex justify-end pt-3">

            <button type="button" wire:click="confirm"
                class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
                <span wire:loading.remove wire:target="confirm">
                    Konfimasi
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
</div>

@script
    <script>
        const notif = new Audio("{{ asset('assets/sound.wav') }}")
        $wire.on('playSound', () => {
            console.log('play');
            notif.play();
        });
        $wire.on('SJFocus', (event) => {
            setTimeout(function() {
                $("#surat_jalan").focus()
            }, 50);
        });
        $wire.on('materialFocus', (event) => {
            setTimeout(function() {
                $("#materialNoScan").focus()
            }, 50);
        });
        $wire.on('newItem', (event) => {
            // jika item duplicate
            if (event[0].update) {
                let locationValue = null
                const lineValue = event[0].line ?? null
                const locationSet = event[0].locationSet

                if (event[0].loc_cd) locationValue = event[0].loc_cd
                linehtml = '<div class="flex flex-col w-1/2 mx-auto"><strong>Line C</strong>'
                lokasihtml = '<div class="flex flex-col w-1/2 mx-auto"><strong>Location</strong>'
                if (lineValue !== null) {
                    if (lineValue.length > 1) {
                        linehtml += '<select id="swal-input2" class="swal2-input my-2" >'
                        lineValue.map((i) => {
                            linehtml += '<option value="' + i.line_c + '">' + i.line_c + '</option>'
                        })
                        linehtml += '</select>'
                    } else {
                        linehtml +=
                            `<input id="swal-input2" class="swal2-input" value="${lineValue[0].line_c}" >`
                    }
                }

                if (locationSet) {
                    lokasihtml += `<input id="swal-input3" class="swal2-input" value="${locationSet[0]}" readonly>`
                } else {
                    locationData = ['ASSY', 'CNC']
                    lokasihtml += '<select id="swal-input3" class="swal2-input my-2" >'
                    locationData.map((i) => {
                        lokasihtml += '<option value="' + i + '">' + i + '</option>'
                    })
                    lokasihtml += '</select>'

                }

                linehtml += '</div>'

                if (event[0].line[0].setup_by === 'PO COT') {
                    html = `<div class="flex flex-col">
                                <strong>Qty</strong>
                                <input id="swal-input1" class="swal2-input">
                            </div>
                            ${linehtml}
                            ${lokasihtml}
                            `
                } else {
                    html = `
                                <div class="flex flex-col">
                                    <strong>Qty</strong>
                                    <input id="swal-input1" class="swal2-input">
                                    </div>
                                    <input id="swal-input2" class="swal2-input" hidden>
                                <div class="flex flex-col">
                                    <strong>Location</strong>
                                    <input id="swal-input3" class="swal2-input" value="${locationValue}" >
                                </div>`
                }
                return Swal.fire({
                    title: event[0].title,
                    html: `${html}`,
                    showDenyButton: true,
                    denyButtonText: `Don't save`,
                    didOpen: () => {
                        $('#swal-input1').focus()
                    },
                    preConfirm: () => {
                        return [
                            document.getElementById("swal-input1").value,
                            document.getElementById("swal-input2").value,
                            document.getElementById("swal-input3").value
                        ];
                    }

                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $wire.dispatch('insertNew', {
                            reqData: {

                                qty: result.value[0],
                                lineNew: result.value[1],
                                location: result.value[2],
                            },
                            update: true,
                            save: false
                        })
                        return Swal.fire({
                            timer: 2000,
                            title: "Updated",
                            icon: "success",
                            showConfirmButton: false,
                            timerProgressBar: true,
                        });
                    } else if (result.isDenied) {
                        $wire.dispatch('insertNew', {
                            save: false
                        })
                        return Swal.fire({
                            timer: 1000,
                            title: "Changes are not saved",
                            icon: "info",
                            showConfirmButton: false,
                            timerProgressBar: true,
                        });
                    }
                });

            }
        });
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
        $wire.on('confirmation', (event) => {
            Swal.fire({
                title: "Scan dengan Surat Jalan dan PO yang sama ?",
                showDenyButton: true,
                showCancelButton: true,
                showCancelButton: false,
                confirmButtonText: "Ya",
                denyButtonText: `Tidak`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    Swal.fire("Surat Jalan Sama", "", "info");
                    $wire.dispatch('resetConfirm', {
                        type: 0
                    })
                } else if (result.isDenied) {
                    Swal.fire("Reset Semua", "", "info");
                    $wire.dispatch('resetConfirm', {
                        type: 1
                    })
                }
            });
        });
    </script>
@endscript
