<div class="dark:text-white max-w-7xl mx-auto">

    <div class="text-2xl text-center font-extrabold py-6" id="setHerePagination">Stock Material</div>

    <div class=" overflow-x-auto sm:rounded-lg">
        <div class="flex justify-between py-6">

            <div class="flex  gap-4">
            <div class="">
                <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date
                </label>
                <div class="flex items-center">
                    <div class="relative" wire:ignore>

                        <input id="dtstart" type="date" wire:model="dateStart" onfocus="this.showPicker()"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Select date start">
                    </div>
                    <span class="mx-2 text-gray-500">to</span>
                    <div class="relative" wire:ignore>
                        <input id="dtend" type="date" wire:model="dateEnd" onfocus="this.showPicker()"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Select date end">
                    </div>
                </div>
            </div>
            <div class="">
                <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Material
                    No
                </label>
                <input wire:model="searchMat" wire:keydown.debounce.300ms="matChange" type="text"
                    @if ($matDisable) disabled @endif autocomplete="off"
                    class="@if ($matDisable) bg-gray-100 @endif block w-full p-2 text-gray-900 border border-gray-300 rounded-lg   text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                <div class="absolute">
                    <div class="py-3 text-center bg-green-100 text-green-700 rounded-lg" wire:loading.block
                        wire:target="matChange">Searching</div>
                    <div wire:loading.remove class="rounded-lg bg-slate-50 shadow">

                        @if (strlen($searchMat) >= 3 && !$matDisable)
                            @forelse ($listMaterial as $p)
                                <div class="py-1 px-3 text-base hover:bg-blue-200 rounded-lg" role="button"
                                    wire:click="chooseMat('{{ $p->material }}')">{{ $p->material }}
                                </div>
                            @empty
                                <div class="py-3 text-center text-base bg-red-200 rounded-lg">Tidak Ditemukan</div>
                            @endforelse
                        @endif
                    </div>
                </div>

            </div>
            </div>

            <div class=" justify-end">
                <button wire:click="showData()"
                    class="relative inline-flex items-center justify-center  overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-cyan-500 to-blue-500 group-hover:from-cyan-500 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">
                    <span
                        class="relative px-5 py-2 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                        Show Data
                    </span>
                </button>
            </div>
        </div>

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Material Code
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Tanggal
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Lokasi
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            In
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Out
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Qty Balance
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Qty Now
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($listData as $d)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">
                            {{ $d->material_no }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->tgl }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->loc_cd }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->qty_in }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->qty_out }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->qty_balance }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->qty_now }}
                        </td>
                        {{-- <td class="flex gap-5">
                            <button type="button" onclick="editLokasi('{{ $d->Code }}','{{ $d->Location }}')"
                                class="text-white bg-yellow-400 hover:bg-yellow-800 font-medium rounded-full text-sm px-3 py-1 text-center">Edit
                                Lokasi</button>
                            <button type="button" onclick="openModal('{{ $d->Code }}')"
                                class="text-white bg-blue-400 hover:bg-blue-800 font-medium rounded-full text-sm px-3 py-1 text-center">Cetak
                                Kartu Stok</button>

                        </td> --}}
                    </tr>
                @empty
                    <tr>
                        <td class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 text-center font-bold text-xl py-4"
                            colspan="7">Tidak ada Data
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if (count($listData) > 0)
            {{ $listData->links() }}
        @endif
    </div>

    <div class="fixed left-0 top-0  h-screen w-screen flex justify-center items-center bg-slate-300/70 z-50"
        wire:loading.flex wire:target="exportExcel,exportPdf,gotoPage,nextPage,previousPage,setPerPage">
        <div class="absolute animate-spin rounded-full h-32 w-32 border-t-4 border-b-4 border-purple-500"></div>
        <img src="https://www.svgrepo.com/show/509001/avatar-thinking-9.svg" class="rounded-full h-28 w-28">
    </div>



</div>
