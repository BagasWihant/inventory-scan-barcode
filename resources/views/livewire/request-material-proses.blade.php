<div class="max-w-7xl mx-auto">
    <div class="text-2xl text-center font-extrabold py-6" id="setHerePagination">Proses Material Request</div>

    <div class="relative w-full">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
        </div>
        <input type="text" id="search" wire:model.live.debounce.300ms="searchKey"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Transaksi No." />
    </div>
    <div>
        <div class="relative overflow-x-auto shadow-md rounded-lg my-4" x-data="tableManager()">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-gray-900 uppercase bg-gray-300">
                    <tr>
                        <th scope="col" class="px-1 py-3">
                            Transaksi No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Notes
                        </th>
                        <th scope="col" class="px-6 py-3">

                        </th>
                        <th scope="col" class="px-6 py-3">
                            Form
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $d)
                        <tr
                            class="py-2 @if ($d->type == 2) bg-red-700 text-white font-semibold hover:bg-red-800 @endif hover:bg-gray-200">
                            <td class="px-2" role="button" @click="showMaterialDetails('{{ $d->transaksi_no }}')">
                                {{ $d->transaksi_no }}
                            </td>
                            <td role="button" @click="showMaterialDetails('{{ $d->transaksi_no }}')">
                                {{ $d->type == 2 ? 'Urgent' : 'Reguler' }}
                            </td>
                            <td></td>
                            <td>
                                <button class="bg-blue-600 px-4 py-2 text-white rounded-md"
                                    wire:click="print('{{ $d->transaksi_no }}')">Print</button>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

            <!-- Main modal -->
            <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" x-show="showModal"
                x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="scale-75 backdrop-blur-sm"
                x-transition:enter-end=" scale-100 backdrop-blur-md"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start=" scale-100"
                x-transition:leave-end="scale-75"
                class="flex inset-0 backdrop-blur-md overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 max-h-full">
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
                            @if ($transaksiSelected && $transaksiSelected[0]->status != 1)
                                <div class="px-6 py-2">
                                    <label for="large-input"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Material
                                        No.
                                    </label>
                                    <input type="text" wire:model="materialScan"
                                        wire:keydown.debounce.300ms="prosesScan"
                                        class=" block w-1/4 p-2 text-gray-900 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                </div>
                            @endif
                        </div>

                        <div class="p-3">
                            <div class="relative overflow-y-auto shadow-md rounded-lg my-4">
                                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    <thead class="text-gray-900 uppercase bg-gray-300">
                                        <tr>
                                            <th scope="col" class="px-3 py-3">
                                                Material No
                                            </th>
                                            <th scope="col" class="px-3 py-3">
                                                Material Name
                                            </th>
                                            <th scope="col" class="px-3 py-3">
                                                Unit
                                            </th>
                                            <th scope="col" class="px-3 py-3">
                                                Qty Request
                                            </th>
                                            <th scope="col" class="px-3 py-3">
                                                Qty Supply
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($transaksiSelected)
                                            @foreach ($transaksiSelected as $data)
                                                <tr
                                                    class="@if ($data->request_qty == $data->qty_supply) bg-green-500 text-white @endif">
                                                    <td class="px-3 py-2">{{ $data->material_no }}</td>
                                                    <td class="px-3 py-2">{{ $data->material_name }}</td>
                                                    <td class="px-3 py-2">{{ $data->iss_unit }}</td>
                                                    <td class="px-3 py-2">{{ $data->request_qty }}</td>
                                                    <td class="px-3 py-2" role="button"
                                                        @click="modalQtyEdit(@js($data))">
                                                        {{ $data->qty_supply }}</td>

                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600 sticky bottom-0 bg-white">

                            <button @click="closeModal" type="button"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Close</button>
                            <button type="button" wire:click="saveDetailScanned"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-50 focus:outline-none bg-blue-500 rounded-lg border  hover:bg-blue-600 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div wire:loading.flex
            class=" fixed z-[99] bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
            wire:target="materialScan" aria-label="Loading..." role="status">
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
        <script>
            function modalQtyEdit(data) {
                Swal.fire({
                    timer: event[0].time,
                    title: event[0].title,
                    icon: event[0].icon,
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            }

            function tableManager() {
                return {
                    showModal: false,
                    showMaterialDetails(trx) {
                        // Use Livewire to fetch only when needed
                        @this.call('getMaterial', trx).then((data) => {
                            this.showModal = true
                        });
                    },
                    closeModal() {
                        @this.materialScan = null;
                        @this.transaksiSelected = null;
                        this.showModal = false
                    }
                }
            }
        </script>
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
