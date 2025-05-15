<div class="max-w-7xl mx-auto">
    <div class="text-2xl text-center font-extrabold py-6" id="setHerePagination">Receiving Request</div>

    <div class="relative w-full">
        {{-- <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
        </div>
        <input type="text" id="search" wire:model.live.debounce.300ms="searchKey"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Transaksi No." /> --}}
    </div>
    <div>
        <div class="relative overflow-x-auto shadow-md rounded-lg my-4" x-data="{
            transaksiSelected: [],
            editQtyName: null,
            editedQty: {},
            showModal: false,
            addButton: false,
            showMaterialDetails(trx) {
                @this.call('getMaterial', trx).then((data) => {
                    this.showModal = true
                    this.transaksiSelected = data[0]
                    this.addButton = data[1]
                });
            },
            closeModal() {
                this.transaksiSelected = [];
                this.showModal = false
                this.showForm = false
            },
            saveDetailScanned() {
                @this.call('saveDetailScanned').then((data) => {
                    if (data.success) {
                        return this.showModal = false
                    }
                    return Swal.fire({
                        timer: 2500,
                        title: data.message,
                        icon: 'error',
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
        
                })
            },
            resetQty(material) {
                @this.call('resetQty', material)
            },
            startEditing(material) {
                this.editQtyName = material
            },
            stopEditing(material, value) {
                const selected = this.transaksiSelected.find(m => m.material_no === material);
                if (selected) {
                    if (selected.qty_supply < value) {
                        Swal.fire({
                            timer: 1000,
                            title: `Qty Request tidak boleh lebih besar dari  ${selected.qty_supply}`,
                            icon: 'error',
                            showConfirmButton: false,
                            timerProgressBar: true,
                        });
                        return;
                    }
                    selected.qty_receive = value;
                }
                this.editQtyName = null;
            },
            resetQty(material) {
                const selected = this.transaksiSelected.find(m => m.material_no === material);
                if (selected) {
                    selected.qty_receive = selected.qty_supply;
                }
            },
            saveDetailScanned() {
                if (this.penerima.nik == null) {
                    alert('Penerima tidak boleh kosong');
                    return
                }
                @this.call('saveDetailScanned', this.transaksiSelected, this.penerima)
                this.closeModal()
                this.showForm = false
            },
            showForm: false,
            showFormStep2: false,
            form: {
                material_no: '',
                material_name: '',
                request_qty: 0,
                qty_supply: 0,
                qty_receive: 0,
            },
            addMaterial() {
                if (!this.form.material_no) {
                    alert('Material No are required.');
                    return;
                }
                this.form.setup_id = this.transaksiSelected[0].setup_id;
                this.form.penanggung_jawab = this.penanggungJawab.nama;
                this.transaksiSelected.push({ ...this.form });
                @this.call('addMaterialDetail', this.form, this.transaksiSelected)
        
                this.form = {
                    material_no: '',
                    material_name: '-',
                    request_qty: 0,
                    qty_supply: 0,
                    qty_receive: 0,
                };
                this.showForm = false;
                this.penanggungJawab = {
                    nik: null,
                    nama: null,
                };
                this.showFormStep2 = false;
            },
            inputPenerima: '',
            penerima: {
                nik: null,
                nama: null,
            },
            penanggungJawab: {
                nik: null,
                nama: null,
            },
            openListPenerima: false,
            listPenerima: [],
            searchPenerima() {
            console.log(this.penerima)
                @this.call('searchPenerima', this.inputPenerima).then((res) => {
                    this.openListPenerima = true
                    this.listPenerima = res
                })
            },
            pilihPenerima(penerima) {
                this.inputPenerima = ''
                this.openListPenerima = false
                this.penerima = penerima
            },
            pilihPenanggungJawab(penerima) {
                this.inputPenerima = ''
                this.openListPenerima = false
                this.penanggungJawab = penerima
            }
        }">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-gray-900 uppercase bg-gray-300">
                    <tr>
                        <th scope="col" class="px-1 py-3">
                            Transaksi No
                        </th>
                        <th scope="col" class="px-1 py-3">
                            Issue Date
                        </th>
                        <th scope="col" class="px-1 py-3">
                            Line Code
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Notes
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Form
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $d)
                        <tr
                            class="py-2 
                            @if ($d->status == 1) bg-red-700 text-white font-semibold hover:bg-red-800 
                            @elseif ($d->status == 2) bg-green-700 text-white font-semibold hover:bg-green-800 @endif ">
                            <td class="px-2" role="button" @click="showMaterialDetails('{{ $d->transaksi_no }}')">
                                {{ $d->transaksi_no }}
                            </td>
                            <td role="button" @click="showMaterialDetails('{{ $d->transaksi_no }}')">
                                {{ $d->issue_date }}
                            </td>
                            <td role="button" @click="showMaterialDetails('{{ $d->transaksi_no }}')">
                                {{ $d->line_c }}
                            </td>
                            <td role="button" @click="showMaterialDetails('{{ $d->transaksi_no }}')">
                                {{ $d->type == 2 ? 'Urgent' : 'Reguler' }}
                            </td>
                            <td>
                                @if ($d->status == 1)
                                    Belum Supply
                                @elseif($d->status == 2)
                                    Sudah Supply
                                @endif
                            </td>
                            <td>
                                <button class="bg-blue-600 px-4 py-2 text-white rounded-md"
                                    wire:click="print('{{ $d->transaksi_no }}')">Print</button>
                            </td>
                        </tr>
                    @endforeach

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
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="sticky top-0 z-40 bg-white">
                            <div
                                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 ">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail
                                    {{ $transaksiNo }} </h3>
                                <button type="button" @click="closeModal"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>

                        </div>
                        <div  x-effect="console.log('penerima updated', penerima)" class="flex justify-between" x-show="showForm == false">
                            <div class="w-full">
                                <div x-show="penerima.nik == null">
                                    <div class="px-6 py-2">
                                        <label for="large-input"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                                            Penerima
                                        </label>
                                        <input type="text" x-model="inputPenerima" placeholder="Search Here..."
                                            x-on:input="searchPenerima"
                                            class=" block w-1/4 p-2 text-gray-900 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                    <div class="absolute shadow-md z-10 w-1/2 left-3" x-show="openListPenerima">

                                        <ul x-on:click.outside="openListPenerima = !openListPenerima"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 translate"
                                            x-transition:enter-end="opacity-100 translate"
                                            x-transition:leave="transition ease-in duration-300"
                                            x-transition:leave-start="opacity-100 translate"
                                            x-transition:leave-end="opacity-0 translate" class="w-full cursor-pointer">
                                            <template x-for="(lp,i) in listPenerima" :key="i">
                                                <li class="w-full text-gray-700 p-2 bg-white  hover:bg-blue-200 rounded-sm"
                                                    @click="pilihPenerima(lp)" x-text="lp.nik + ' | ' + lp.nama"></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                                <div x-show="penerima.nik != null">
                                    <div class="px-6 py-2">
                                        <p class="mb-2 text-lg font-medium text-gray-900"
                                            x-text="'Nik Penerima : ' + penerima.nik"></p>
                                        <p class="mb-2 text-lg font-medium text-gray-900"
                                            x-text="'Nama Penerima : ' + penerima.nama"></p>
                                        <button class="bg-red-600 shadow-md text-white p-1 rounded-lg text-sm"
                                            @click="penerima.nama = null;penerima.nik = null; inputPenerima = null">Reset</button>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full flex justify-end p-3" x-show="addButton == 1">
                                <button class="btn bg-blue-500 shadow-md text-white p-2 rounded-lg h-10"
                                    x-show="showForm == false" @click="showForm = !showForm" x-transition>Add
                                    Material</button>
                            </div>
                        </div>
                        <div class="p-3" x-show="showForm" x-transition>

                            <div class="bg-gray-100 p-4 rounded-lg shadow-md space-y-3">
                                {{-- SET PENANGGUNG JAWAB --}}
                                <div class="flex" x-show="showFormStep2 == false">
                                    <div class="px-6 py-2" x-show="penanggungJawab.nik == null">
                                        <label for="large-input"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama
                                            Penanggung Jawab
                                        </label>
                                        <input type="text" x-model="inputPenerima" placeholder="Search Here..."
                                            x-on:input="searchPenerima"
                                            class=" block w-1/2 p-2 text-gray-900 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    </div>
                                    <div class="">
                                        <div x-show="penanggungJawab.nik != null">
                                            <div class="px-6 py-2">
                                                <p class="mb-2 text-lg font-medium text-gray-900"
                                                    x-text="'Nik Penanggung Jawab : ' + penanggungJawab.nik"></p>
                                                <p class="mb-2 text-lg font-medium text-gray-900"
                                                    x-text="'Nama Penanggung Jawab : ' + penanggungJawab.nama"></p>
                                                <button class="bg-red-600 shadow-md text-white p-1 rounded-lg text-sm"
                                                    @click="penanggungJawab.nama = null;penanggungJawab.nik = null; inputPenerima = null;">Reset</button>
                                                <button class="bg-blue-500 shadow-md text-white p-1 rounded-lg text-sm"
                                                    @click="showFormStep2 = !showFormStep2">Set Penanggung
                                                    Jawab</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="absolute shadow-md z-10 w-1/2 left-3 -bottom-12"
                                        x-show="openListPenerima">

                                        <ul x-on:click.outside="openListPenerima = !openListPenerima"
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 translate"
                                            x-transition:enter-end="opacity-100 translate"
                                            x-transition:leave="transition ease-in duration-300"
                                            x-transition:leave-start="opacity-100 translate"
                                            x-transition:leave-end="opacity-0 translate"
                                            class="w-full cursor-pointer">
                                            <template x-for="(lp,i) in listPenerima" :key="i">
                                                <li class="w-full text-gray-700 p-2 bg-white  hover:bg-blue-200 rounded-sm"
                                                    @click="pilihPenanggungJawab(lp)"
                                                    x-text="lp.nik + ' | ' + lp.nama"></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                                <div class="" x-show="showFormStep2 && penanggungJawab.nik != null">
                                    <p class="mb-2 text-lg font-medium text-gray-900"
                                        x-text="'Nik Penanggung Jawab : ' + penanggungJawab.nik"></p>
                                    <p class="mb-2 text-lg font-medium text-gray-900"
                                        x-text="'Nama Penanggung Jawab : ' + penanggungJawab.nama"></p>
                                    <label for="">Material No</label>
                                    <input type="text" placeholder="Material No" class="w-full p-2 border rounded"
                                        x-model="form.material_no">
                                    <label for="">Material Name</label>
                                    <input type="text" placeholder="Material Name"
                                        class="w-full p-2 border rounded" x-model="form.material_name">
                                    <label for="">Qty Receive</label>
                                    <input type="number" placeholder="Qty Receive"
                                        class="w-full p-2 mb-3 border rounded" x-model.number="form.qty_receive">

                                    <div class="flex justify-end gap-3">
                                        <button class="bg-red-400 text-white px-4 py-2 rounded"
                                            @click="showForm = !showForm">Cancel</button>
                                        <button class="bg-green-600 text-white px-4 py-2 rounded"
                                            @click="addMaterial()">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-3" x-show="showForm == false" x-transition>
                            <div class="relative overflow-y-auto shadow-md rounded-lg my-4">
                                <table
                                    class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead class="text-gray-900 uppercase bg-gray-300">
                                        <tr>
                                            <th scope="col" class="px-3 py-3">
                                                Material No
                                            </th>
                                            <th scope="col" class="px-3 py-3">
                                                Product No
                                            </th>
                                            <th scope="col" class="px-3 py-3">
                                                Material Name
                                            </th>
                                            <th scope="col" class="px-3 py-3">
                                                Qty Request
                                            </th>
                                            <th scope="col" class="px-3 py-3">
                                                Qty Supply
                                            </th>
                                            <th scope="col" class="px-3 py-3">
                                                Qty Receive
                                            </th>
                                            <th>

                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(m,i) in transaksiSelected" :key="i">
                                            <tr
                                                :class="m.request_qty == m.qty_receive ? ' bg-green-500 text-white' :
                                                    ' bg-yellow-400 text-white'">
                                                <td class="px-3 py-2" x-text="m.material_no"></td>
                                                <td class="px-3 py-2" x-text="'-'"></td>
                                                <td class="px-3 py-2" x-text="m.material_name"></td>
                                                <td class="px-3 py-2" x-text="m.request_qty"></td>
                                                <td class="px-3 py-2" x-text="m.qty_supply"></td>
                                                <td class="px-3 py-2"
                                                    @click="transaksiSelected[0]?.status == 1 && startEditing(m.material_no)"
                                                    :class="{
                                                        'cursor-pointer': transaksiSelected[0]?.status == 1,
                                                        'cursor-default': transaksiSelected[0]?.status != 1
                                                    }">
                                                    <template
                                                        x-if="editQtyName === m.material_no && transaksiSelected[0]?.status == 1">
                                                        <input type="number" min="1" :value="m.qty_receive"
                                                            @blur="stopEditing(m.material_no, $event.target.value)"
                                                            @keydown.enter="stopEditing(m.material_no, $event.target.value)"
                                                            class="text-black border border-gray-300 rounded px-2 py-1 w-24" />
                                                    </template>
                                                    <template x-if="editQtyName !== m.material_no ">
                                                        <span x-text="m.qty_receive"></span>
                                                    </template>
                                                </td>
                                                <td>
                                                    <template x-if="transaksiSelected[0]?.status == 1">
                                                        <button class="bg-yellow-600 px-4 py-1 text-white rounded-md"
                                                            @click="resetQty(m.material_no)">Reset</button>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600 sticky bottom-0 bg-white">

                            <button @click="closeModal" type="button"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Close</button>
                            <template x-if="transaksiSelected[0]?.status == 1 ">
                                <button type="button" @click="saveDetailScanned"
                                    class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-50 focus:outline-none bg-blue-500 rounded-lg border  hover:bg-blue-600 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Confirm</button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div wire:loading.flex
            class=" fixed z-[99] bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
            wire:target="materialScan,saveDetailScanned,resetQty" aria-label="Loading..." role="status">
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

                $wire.on('qtyInput', (event) => {
                    return Swal.fire({
                        title: event[0].title,
                        html: `<div class="flex flex-col">
                                <strong>Qty</strong>
                                <input id="swal-input1" class="swal2-input">
                            </div>`,
                        showDenyButton: true,
                        denyButtonText: `Don't save`,
                        didOpen: () => {
                            $('#swal-input1').focus()
                            $('#swal-input1').on('keydown', (e) => {
                                if (e.key == 'Enter') {
                                    Swal.clickConfirm();
                                }
                            })
                        },
                        preConfirm: () => {
                            return [
                                document.getElementById("swal-input1").value,
                            ];
                        }

                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            $wire.dispatch('inputQty', {
                                qty: result.value[0]
                            })
                            // return Swal.fire({
                            //     timer: 2000,
                            //     title: "Material Added",
                            //     icon: "success",
                            //     showConfirmButton: false,
                            //     timerProgressBar: true,
                            // });
                        } else if (result.isDenied) {
                            @this.getMaterial(event[0].trx)
                            return Swal.fire({
                                timer: 1000,
                                title: "Changes are not saved",
                                icon: "info",
                                showConfirmButton: false,
                                timerProgressBar: true,
                            });
                        }
                    });
                })
            </script>
        @endscript
    </div>
</div>
