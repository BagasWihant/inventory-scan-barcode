<div class="p-4" x-data="alpinejs()" x-init="window.addEventListener('data-load', e => {
    inputDisable.search = true;
    materialNo = e.detail[0];
})">
    <div class="flex my-4 gap-4 items-center">
        <div class="flex-col">

            <x-search-dropdown :method="'searchMaterial'" :onSelect="'selectMaterial'" :label="'Material No'" :resetEvent="'reset-search'" :field="'matl_no'"
                x-bind:disabled="inputDisable.search"
                x-bind:class="{ 'bg-gray-100 text-gray-800': inputDisable.search, 'bg-white text-black': !inputDisable.search }" />

        </div>

        <div class="flex-col">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date Start</label>
            <input x-model="datestart" type="date" onfocus="this.showPicker()" :disabled="inputDisable.date"
                id="date"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 sm:text-sm"
                :class="{ 'bg-gray-100 text-black': inputDisable.date, 'bg-white text-black': !inputDisable.date }">
        </div>

        <div class="flex-col">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date End</label>

            <input x-model="dateend" type="date" onfocus="this.showPicker()" :disabled="inputDisable.date"
                id="date"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 sm:text-sm"
                :class="{ 'bg-gray-100 text-black': inputDisable.date, 'bg-white text-black': !inputDisable.date }">
        </div>

        <div class="flex items-end ml-auto gap-4">
            @if (!empty($dataJson))
                <button @click="resetData" type="button"
                    class="text-white bg-gradient-to-r from-red-500 to-red-600 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
                    Reset
                </button>
            @else
                <button @click="showData" type="button"
                    class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
                    Cari
                </button>
            @endif
        </div>
    </div>
    
    <div x-show="isLoading" x-cloak class="flex flex-col items-center justify-center gap-2 mt-7">
        <svg class="animate-spin h-10 w-10 text-black" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
            </path>
        </svg>
        Load Data...
    </div>
    {{-- Wajib nggo livewire, alpine eror pagination soale --}}
    @if (!empty($dataJson))
        <div class="overflow-x-auto pb-3">
            <table class="table-fixed border-collapse w-full text-sm">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-slate-400 px-2 py-1 w-32 break-words">Material</th>
                        <th class="border border-slate-400 px-2 py-1 w-16 break-words">Stok Actual</th>
                        <th class="border border-slate-400 px-2 py-1 w-20">Type</th>
                        @foreach ($tanggal as $d)
                            <th
                                class="border border-slate-400 px-2 py-1 w-14 break-words text-xs {{ $d == $today ? 'bg-blue-400 font-bold text-white' : '' }}">
                                {{ \Carbon\Carbon::parse($d)->format('d/m/Y') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataJson as $materialNo => $item)
                        @php
                            $rowTypes = ['receiving', 'supply', 'plan_cnc', 'stock_cnc', 'stock_mc'];
                            $rowLabels = [
                                'receiving' => 'Receiving',
                                'supply' => 'Supply',
                                'plan_cnc' => 'Plan CNC',
                                'stock_cnc' => 'Stock CNC',
                                'stock_mc' => 'Stock MC',
                            ];
                        @endphp

                        @foreach ($rowTypes as $index => $rowType)
                            <tr>
                                {{-- material  --}}
                                @if ($index === 0)
                                    <td rowspan="5"
                                        class="px-2 py-1 border-2 border-r border-r-slate-400 border-black">
                                        {{ $item['material_no'] }}
                                    </td>
                                @endif
                                {{-- stok --}}
                                @if ($index === 0)
                                    <td rowspan="5"
                                        class="border-t-2 border-b-2 border-b-black border-t-black border border-slate-300 px-2 py-1">
                                        {{ $item['qty_wip'] }}
                                    </td>
                                @endif
                                {{-- tipe --}}
                                <td
                                    class="border px-2 py-1 bg-yellow-200 
                                    @if ($rowType === 'receiving') border-t-2 border-t-black border border-slate-300
                                    @elseif(in_array($rowType, ['supply', 'plan_cnc', 'stock_cnc'])) border-slate-300 border
                                    @elseif($rowType === 'stock_mc') border-b-2 border-b-black border-slate-300 border @endif">
                                    <b>{{ $rowLabels[$rowType] }}</b>
                                </td>

                                {{-- tanggale --}}
                                @foreach ($tanggal as $d)
                                    <td
                                        class="border px-2 py-1
                                        @if ($rowType === 'receiving') border-t-2 border-t-black border border-slate-300
                                        @elseif(in_array($rowType, ['supply', 'plan_cnc', 'stock_cnc'])) border-slate-300 border
                                        @elseif($rowType === 'stock_mc') border-b-2 border-b-black border-slate-300 border @endif">
                                        @php
                                            $dataToday = $item['tanggal'][$d] ?? [];
                                            $value = '-';

                                            if ($rowType === 'receiving') {
                                                $value = $dataToday['receiving']->picking_qty ?? '-';
                                            } elseif ($rowType === 'supply') {
                                                $value = $dataToday['supply']->qty ?? '-';
                                            } elseif ($rowType === 'plan_cnc') {
                                                if (!empty($dataToday['wip'])) {
                                                    $qty = $dataToday['wip']['qty'] ?? 0;
                                                    $bomQty = $dataToday['wip']['bom_qty'] ?? 1;
                                                    $value = $qty * $bomQty;
                                                }
                                            } elseif ($rowType === 'stock_cnc') {
                                                $value = $dataToday['stock_cnc'] ?? '-';
                                            } elseif ($rowType === 'stock_mc') {
                                                $value = $dataToday['stock_mc'] ?? '-';
                                            }
                                        @endphp
                                        {{ $value }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($currentPage < $lastPage || $currentPage > 1)
                <div class="flex gap-2 mt-4">
                    @if ($currentPage > 1)
                        <button wire:click="prevPage" type="button"
                            class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center">
                            Prev &lt;
                        </button>
                    @endif

                    <span class="flex items-center px-3">{{ $currentPage }}</span>

                    @if ($currentPage < $lastPage)
                        <button wire:click="nextPage" type="button"
                            class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center">
                            Next &gt;
                        </button>

                        <button wire:click="gotoLastPage" type="button"
                            class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center">
                            Last - {{ $lastPage }}
                        </button>
                    @endif
                </div>
            @endif
        </div>
    @endif

    @if (empty($dataJson))
        <div class="p-4">
            <p class="text-center text-gray-500">Tidak ada data — klik "Cari"</p>
        </div>
    @endif
</div>
<script>
    function alpinejs() {
        return {
            inputDisable: {
                search: false,
                date: false,
            },

            datestart: null,
            dateend: null,
            isLoading: false,

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

            showData() {

                if (!this.datestart) return this.showAlert('Mohon isi Date Start');
                if (!this.dateend) return this.showAlert('Mohon isi Date End');

                this.isLoading = true

                @this.call('showData', this.datestart, this.dateend, this.materialNo).then((data) => {
                    this.isLoading = false
                    this.inputDisable.date = true
                })
            },

            resetData() {
                @this.call('resetData')
                this.$dispatch('reset-search');
                this.inputDisable.search = false
                this.inputDisable.date = false
                this.datestart = null
                this.dateend = null
            }
        }
    }
</script>
