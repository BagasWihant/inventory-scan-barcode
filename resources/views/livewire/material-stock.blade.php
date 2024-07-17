<div class="dark:text-white max-w-7xl mx-auto">

    <div class="text-2xl text-center font-extrabold py-6" id="setHerePagination">Stock Material</div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <div class="flex justify-between">

            <div class="flex gap-4">

                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" id="search" wire:model.live.debounce.300ms="searchKey"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search here..." />
                </div>

                <div class="w-1/3" wire:ignore>
                    <select id="materialselect" style="width: 100%" wire:model="perPage" wire:change="setPerPage"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full !p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>

            </div>
            <div class="">
                <button type="button" wire:click="exportExcel"
                    class="text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Excel</button>
            </div>
        </div>

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Code
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Name
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Wire Code
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Location
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Satuan
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Min Lot
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Qty
                        </div>
                    </th>
                    <th scope="col" class="w-1/5">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody wire:ignore.self>
                @foreach ($datas as $d)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">
                            {{ $d->Code }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->Name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->WireCode }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->Location }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->Satuan }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->MinLot }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->Qty }}
                        </td>
                        <td class="flex gap-5">
                            <button type="button" onclick="editLokasi({{ $d->Code }},'{{ $d->Location }}')"
                                class="text-white bg-yellow-400 hover:bg-yellow-800 font-medium rounded-full text-sm px-3 py-1 text-center">Edit
                                Lokasi</button>
                            <button type="button" onclick="openModal('{{ $d->Code }}')"
                                class="text-white bg-blue-400 hover:bg-blue-800 font-medium rounded-full text-sm px-3 py-1 text-center">Cetak
                                Kartu Stok</button>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="fixed left-0 top-0  h-screen w-screen flex justify-center items-center bg-slate-300/70 z-50"
        wire:loading.flex wire:target="exportExcel,exportPdf,gotoPage,nextPage,previousPage,setPerPage">
        <div class="absolute animate-spin rounded-full h-32 w-32 border-t-4 border-b-4 border-purple-500"></div>
        <img src="https://www.svgrepo.com/show/509001/avatar-thinking-9.svg" class="rounded-full h-28 w-28">
    </div>


    {{-- @if ($modal) --}}
    <!-- Main modal -->
    <div id="overlayModal" tabindex="-1" aria-hidden="true" wire:ignore.self
        class="hidden max-h-screen bg-slate-200/60 backdrop-filter backdrop-blur-sm overflow-hiden hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] ">
        <div wire:ignore.self class="relative p-4 opacity-0 transform -translate-y-full scale-150 bg-white rounded-xl shadow-lg  transition-transform duration-200"
            id="modal">
            <!-- Modal content -->
            <div class="relative max-h-screen">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Detail Material {{ $modalName }}
                    </h3>
                    <button onclick="closeModal()" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="overflow-y-auto max-h-[568px]">
                    <!-- Modal body -->
                    <table class="w-full max-h-screen text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="p-1">
                                    Tanggal
                                </th>
                                <th scope="col" class="p-1">
                                    Trucking
                                </th>
                                <th scope="col" class="p-1">
                                    Receive
                                </th>
                                <th scope="col" class="p-1">
                                    In
                                </th>
                                <th scope="col" class="p-1">
                                    Out
                                </th>
                                <th scope="col" class="p-1">
                                    Supply
                                </th>
                                <th scope="col" class="p-1">
                                    Qty
                                </th>
                            </tr>
                        </thead>
                        <tbody wire:ignore.self>
                            @forelse ($detailMaterial as $d)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4">
                                        {{ $d->Tgl }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $d->Trucking }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $d->Receive }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $d->{'Qty IN'} }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $d->{'Qty Out'} }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $d->Supply }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $d->Qty }}
                                    </td>
                                </tr>
                            @empty
                                <span class="text-xl text-center font-bold py-4">Belum Ada Data</span>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- @endif --}}



</div>
<script>
    function editLokasi(id, lokasi) {
        Swal.fire({
            title: 'Edit Lokasi',
            input: "text",
            inputValue: lokasi,
            showDenyButton: true,
            denyButtonText: `Don't save`
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                @this.editLokasi(id, result.value)

                return Swal.fire({
                    timer: 1000,
                    title: "Updated",
                    icon: "success",
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            } else if (result.isDenied) {
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

    const modal_overlay = document.querySelector('#overlayModal');
    const modal = document.querySelector('#modal');
    const modalCl = modal.classList
    const overlayCl = modal_overlay

    function openModal(co) {
        @this.printMaterial(co)
        
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
        }, 100);
        setTimeout(() => overlayCl.classList.add('hidden'), 300);
        @this.closeModal()
    }
</script>
@script
    <script>
        $wire.on('searchFocus', (event) => {
            $("#search").focus()
        });
    </script>
@endscript
