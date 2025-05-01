<div class="max-w-7xl mx-auto" x-data="{
    todayCount: 0,
    showModal: false,
    data: [],
    dataDetail: [],
    editQty: null,
    editedQty: {},
    loadData() {
        @this.call('loadData').then((res) => {
            this.data = res;
        })
    },
    showMaterialDetails(trx) {
        @this.call('getDetail', trx).then((data) => {
            this.showModal = true;
            this.dataDetail = data;
        });
    },
    closeModal() {
        this.showModal = false
        this.dataDetail = []
    },

    saveDetailScanned(){
        @this.call('saveDetailScanned',this.dataDetail[0].no_retur).then((res) => {
             if (res == 'success') {
                Swal.fire({
                    timer: 1000,
                    title: `Retur Berhasil di proses`,
                    icon: 'success',
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
                this.loadData();
            } else {
                Swal.fire({
                    timer: 1000,
                    title: `Retur Gagal di proses`,
                    icon: 'error',
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            }
        });
        this.closeModal();
    }
}" x-init="loadData();">
    <div class="text-2xl text-center font-extrabold py-6" id="setHerePagination">Proses Retur Assy</div>

    <div class="flex justify-end"><span>Total Transaksi Hari ini : <b x-text="todayCount"></b></span></div>
    <div class="relative w-full">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
        </div>
        <input type="text" id="search" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Transaksi No." />
    </div>
    <div>
        <div class="relative overflow-x-auto shadow-md rounded-lg my-4">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-gray-900 uppercase bg-gray-300">
                    <tr>
                        <th scope="col" class="px-1 py-3">
                            No Retur
                        </th>
                        <th scope="col" class="px-1 py-3">
                            Issue Date
                        </th>
                        <th scope="col" class="px-1 py-3">
                            Line Code
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="d in data" :key="d.no_retur">
                        <tr :class="d.status === '-' ? 'bg-red-700 text-white font-semibold hover:bg-red-800' :
                            'bg-green-700 text-white font-semibold hover:bg-green-800'"
                            class="py-2 h-10">
                            <td class="px-2" role="button" @click="showMaterialDetails(d.no_retur)">
                                <span x-text="d.no_retur"></span>
                            </td>
                            <td role="button" @click="showMaterialDetails(d.no_retur)">
                                <span x-text="d.issue_date"></span>
                            </td>
                            <td role="button" @click="showMaterialDetails(d.no_retur)">
                                <span x-text="d.line_c"></span>
                            </td>
                            <td role="button" @click="showMaterialDetails(d.no_retur)">
                                <span x-text="d.status == '-'  ? 'Belum diproses' : 'Sudah diproses'"></span>
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>

            <!-- Main modal -->
            <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" x-show="showModal"
                x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="scale-90 backdrop-blur-sm"
                x-transition:enter-end=" scale-100 backdrop-blur-md"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start=" scale-100"
                x-transition:leave-end="scale-90"
                class="flex inset-0 sc backdrop-blur-md overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 max-h-full">
                <div class="relative p-4 w-full max-w-7xl max-h-full">
                    <!-- Modal content -->
                    <template x-if="dataDetail.length >0 ">
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <!-- Modal header -->
                            <div class="sticky top-0 z-40 bg-white">
                                <div
                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 ">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white"
                                        x-text="dataDetail[0].no_retur">Detail</h3>
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
                            <div class="text-center">
                                <span class="text-red-600">Klik qty untuk edit </span>
                            </div>
                            <div class="p-3">
                                <div class="relative overflow-y-auto shadow-md rounded-lg my-4">
                                    <table
                                        class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead class="text-gray-900 uppercase bg-gray-300">
                                            <tr>
                                                <th scope="col" class="px-3 py-3">
                                                    Material No
                                                </th>
                                                <th scope="col" class="px-3 py-3">
                                                    Material Name
                                                </th>
                                                <th scope="col" class="px-3 py-3">
                                                    Line C
                                                </th>
                                                <th scope="col" class="px-3 py-3">
                                                    Qty Retur
                                                </th>
                                                <th>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="d in dataDetail">
                                                <tr>
                                                    <td>
                                                        <span x-text="d.material_no"></span>
                                                    </td>
                                                    <td>
                                                        <span x-text="d.material_name"></span>
                                                    </td>
                                                    <td>
                                                        <span x-text="d.line_c"></span>
                                                    </td>
                                                    <td >
                                                        <span x-text="d.qty"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <div
                                class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600 sticky bottom-0 bg-white">

                                <button @click="closeModal" type="button"
                                    class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Close</button>

                                    <template x-if="dataDetail.length > 0 && dataDetail[0].status == '-' ">

                                        <button type="button" @click="saveDetailScanned"
                                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-50 focus:outline-none bg-blue-500 rounded-lg border  hover:bg-blue-600 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Confirm</button>
                                    </template>

                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div wire:loading.flex
            class=" fixed z-[99] bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
            wire:target="getDetail" aria-label="Loading..." role="status">
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

            </script>
        @endscript
    </div>
</div>
