<div>
    <div class="text-2xl font-extrabold py-6 text-center">Register Palet</div>

    {{-- <div class="flex justify-end">
        <a wire:navigate href="{{ route('create_palet') }}"
            class=" text-base text-white block bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-lg px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Buat
            Pallet Baru</a>
    </div> --}}
    <div class="flex gap-5">
        <div class="w-1/6">
            <input wire:model.live.debounce="searchPalet" type="text" autocomplete="off" placeholder="Search Palet"
                class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg   text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
        <div class="">
            <select wire:model.live="status" class="p-2 text-gray-900 border border-gray-300 rounded-lg  ">
                <option value="-">Status</option>
                <option value="supply">Supply</option>
                <option value="not">Belum Supply</option>
            </select>
        </div>
        <div class="">
            <select wire:model.live="lokasi" class="p-2 text-gray-900 border border-gray-300 rounded-lg  ">
                <option value="-">Lokasi</option>
                <option value="V-01">V-01</option>
                <option value="V-02">V-02</option>
                <option value="V-03">V-03</option>
                <option value="V-04">V-04</option>
            </select>
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
        <span class="text-4xl font-medium text-white">Loading</span>
    </div>

    @if (count($listMaterial) > 0)
        {{-- <div class="flex  gap-4 overflow-x-auto sm:rounded-lg p-3 "> --}}

        <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">List Barang </h2>
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Tanggal Scan
                    </th>
                    <th scope="col" class="px-6 py-3">
                        No Pallet
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Lokasi
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Line C
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Tanggal Supply
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3">

                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listMaterial as $product)
                    <tr class=" border rounded dark:border-gray-700 ">
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->created_at }}</th>

                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->palet_no }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->lokasi }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->line_c }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->supply_date }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->status == 1 ? 'Sudah Supply' : 'Belum Supply' }}
                        </th>
                        <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <button class="bg-blue-500  text-white font-bold py-2 px-4 rounded-lg"
                                wire:click="editLokasi('{{ $product->palet_no }}')">Edit Lokasi</button>
                            <button class="bg-blue-500  text-white font-bold py-2 px-4 rounded-lg"
                                onclick="openModal('{{ $product->palet_no }}')">Detail</button>
                        </th>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $listMaterial->links() }}


        <div id="overlayModal" tabindex="-1" aria-hidden="true" wire:ignore.self
            class="max-h-screen bg-slate-200/60 backdrop-filter backdrop-blur-sm overflow-hiden hidden fixed top-0 right-0 left-0 z-10 justify-center items-center w-full md:inset-0 h-screen ">
            <div wire:ignore.self
                class="relative p-4 opacity-0 transform -translate-y-full scale-150 bg-white rounded-xl shadow-lg  transition-transform duration-200"
                id="modal">
                <!-- Modal content -->
                <div class="relative max-h-screen">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between px-4 md:px-5 rounded-t dark:border-gray-600">
                        <div class="flex justify-between w-full font-bold">
                            <p class="font-bold">{{ $no_palet_modal }}</p>
                            <strong class=" ">{{ $scan_date_modal }} </strong>
                        </div>
                        <button onclick="closeModal()" type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <div class="flex justify-start py-2">
                        <button type="button" wire:click="exportDetailExcel"
                            class="text-white bg-green-500 hover:bg-green-800 font-medium rounded-full text-sm px-3 py-1 text-center">Export
                            Excel</button>
                    </div>
                    <div class="overflow-y-auto max-h-[568px]">
                        <!-- Modal body -->
                        @if (count($listMaterialDetail) > 0)
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            No
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Material no
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Pack
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Total Qty
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listMaterialDetail as $d)
                                        <tr class=" border rounded dark:border-gray-700 ">
                                            <th scope="row"
                                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $loop->iteration }}</th>

                                            <th scope="row"
                                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $d->material }}
                                            </th>
                                            <th scope="row"
                                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $d->pack }}
                                            </th>
                                            <th scope="row"
                                                class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $d->counter }}
                                            </th>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <div
                        class="flex items-center justify-end p-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button type="button" wire:click="print('{{ $no_palet_modal }}')"
                            class="py-2 px-3 ms-3 text-sm font-medium focus:outline-none bg-blue-500 text-white rounded-lg border border-gray-200 hover:bg-blue-200 hover:text-black focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Print</button>
                        <button onclick="closeModal()"
                            class="py-2 px-3 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Close</button>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
<script>
    const modal_overlay = document.querySelector('#overlayModal');
    const modal = document.querySelector('#modal');
    const modalCl = modal.classList
    const overlayCl = modal_overlay

    function openModal(palet) {
        @this.detail(palet)

        overlayCl.classList.remove('hidden')
        overlayCl.classList.add('flex');
        setTimeout(() => {
            modalCl.remove('opacity-0')
            modalCl.remove('-translate-y-full')
            modalCl.remove('scale-150')
        }, 500);

    }

    function closeModal() {
        modalCl.add('-translate-y-full')
        setTimeout(() => {
            modalCl.add('opacity-0')
            modalCl.add('scale-150')
            overlayCl.classList.add('hidden')
        }, 500);
    }
</script>
@script
    <script>
        $wire.on('swalEditLokasi', (event) => {
            html = `<select id="lokasiEdit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <option ${event[0].data.lokasi == '' ? 'selected' : ''} selected>Pilih Lokasi</option>
                        <option ${event[0].data.lokasi == 'V-01' ? 'selected' : ''} value="V-01">V-01</option>
                        <option ${event[0].data.lokasi == 'V-02' ? 'selected' : ''} value="V-02">V-02</option>
                        <option ${event[0].data.lokasi == 'V-03' ? 'selected' : ''} value="V-03">V-03</option>
                        <option ${event[0].data.lokasi == 'V-04' ? 'selected' : ''} value="V-04">V-04</option>
                    </select>`

            Swal.fire({
                title: "Update Lokasi",
                showDenyButton: true,
                showCancelButton: true,
                showCancelButton: false,
                confirmButtonText: "Update",
                denyButtonText: `Tidak`,
                html: html,
                preConfirm: () => {
                    return [
                        document.getElementById("lokasiEdit").value,
                    ];
                }
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "success",
                        title: "Your work has been saved",
                        showConfirmButton: false,
                        timer: 1100
                    });
                    $wire.dispatch('savingUpdateLokasi', {
                        lokasi: result.value[0]
                    })
                } else if (result.isDenied) {
                    Swal.fire({
                        icon: "error",
                        title: "Batal",
                        showConfirmButton: false,
                        timer: 1100
                    });
                }
            });
        })
        $wire.on('showModal', () => {
            // const el = document.getElementById("static-modal")
            // const modal = new Modal(el);
            // modal.show()
        })
    </script>
@endscript
