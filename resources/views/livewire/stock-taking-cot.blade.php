<div class="max-w-7xl m-auto">
    <div class="flex items-end justify-between mb-3 gap-8">
        <div class="flex gap-2">
            <div class="flex-col">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal STO</label>
                <input id="dtstart" type="date" wire:model.live="tglSto" onclick="this.showPicker()"
                    class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Select date start">
            </div>
            <div class="flex-col">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. STO</label>
                <input type="text" wire:model.live="noSto" disabled
                    class=" bg-gray-200  border border-gray-300 text-gray-900 text-sm rounded-lg ">
            </div>
        </div>

        <div class="flex">

            @if ($btnSetupDone)
                <button type="button" wire:click="saveSetup"
                    class="text-white bg-gradient-to-r from-green-600 to-green-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all font-medium rounded-xl text-sm px-5 py-2.5 text-center">
                    Simpan Setup
                </button>
            @endif
            <button type="button" @click="clearData()"
                class="text-white bg-gradient-to-r from-red-600 to-red-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all font-medium rounded-xl text-sm px-5 py-2.5 text-center">
                Clear Data
            </button>
        </div>
    </div>

    <div class="flex justify-between items-end mb-5">
        <div class="flex gap-4">
            <label class="inline-flex items-center me-5 cursor-pointer">
                <input type="checkbox" value="partial" wire:model.live="partial" class="sr-only peer" checked>
                <div
                    class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                </div>
                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Partial</span>
            </label>


            @if ($partial)
                <div class="flex-col">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Material No</label>
                    <input type="text" wire:model="materialNo" wire:keydown.debounce="materialNoScan"
                        class=" bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg "
                        placeholder="Material No (Press '/' to focus)" x-ref="material"
                        @keydown.window="
                if (event.keyCode === 191) {
                    event.preventDefault();
                    $refs.material.focus();
                }">
                </div>
            @else
                <div class="flex-col">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No Pallet</label>
                    <input wire:model="noPalet" wire:keydown.debounce="noPaletScan" type="text"
                        class=" bg-gray-50  border border-gray-300 text-gray-900 text-sm rounded-lg "
                        placeholder="No Pallet (Press '/' to focus)" x-ref="search"
                        @keydown.window="
                if (event.keyCode === 191) {
                    event.preventDefault();
                    $refs.search.focus();
                }">
                </div>
            @endif

        </div>
        <div class="">
            <button type="button" @click="saveAll()"
                class="text-white bg-gradient-to-r from-green-600 to-green-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all font-medium rounded-xl text-sm px-5 py-2.5 text-center">
                Save
            </button>
        </div>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg" x-data="tableManager()">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-2 py-3">
                        No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Line Code
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Material No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Qty
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Palet No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Location
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <span class="sr-only">Edit</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataTable as $data)
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">
                            {{ $loop->iteration }}
                        </td>
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $data->line_code }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $data->material_no }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $data->qty }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $data->palet_no }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $data->location }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="btn bg-yellow-400 text-white rounded-md p-2"
                                @click="editLoc(@js($data))">Edit Location</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" x-show="showModal" x-cloak
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 backdrop-blur-sm"
            x-transition:enter-end="opacity-100 backdrop-blur-md" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="flex inset-0 backdrop-blur-md bg-slate-300/5 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 max-h-full">
            <div class="relative p-4 w-full max-w-xl max-h-full">
                @if ($selectedMaterial != null)
                    <div class="relative bg-white rounded-t-lg shadow dark:bg-gray-700">

                        <div class="p-3">
                            <b>Edit Lokasi {{ $selectedMaterial['material_no'] }}</b>
                        </div>
                    </div>
                    <div class="bg-white">
                        <div class="p-3">
                            <label for="large-input"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Location
                            </label>
                            <select x-ref="location"
                                class="mt-1 p-2 w-full border rounded-md focus:border-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors duration-300">
                                <option @if ($selectedMaterial['location'] == 'V-01') selected @endif value="V-01">V-01</option>
                                <option @if ($selectedMaterial['location'] == 'V-02') selected @endif value="V-02">V-02</option>
                                <option @if ($selectedMaterial['location'] == 'V-03') selected @endif value="V-03">V-03</option>
                                <option @if ($selectedMaterial['location'] == 'V-04') selected @endif value="V-04">V-04</option>
                                <option @if ($selectedMaterial['location'] == 'V-05') selected @endif value="V-05">V-05</option>
                            </select>
                        </div>
                    </div>
                    <div
                        class="flex items-center rounded-b-lg justify-end p-4 md:p-5 border-t border-gray-200 dark:border-gray-600 sticky bottom-0 bg-white">

                        <button @click="closeModal" type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Close</button>
                        <button type="button" @click="changeLocation"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-50 focus:outline-none bg-blue-500 rounded-lg border  hover:bg-blue-600 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Confirm</button>
                    </div>
            </div>
            @endif
        </div>
    </div>
    <div wire:loading.flex
        class=" fixed z-[99] bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        wire:target="materialNoScan,noPaletScan,saveAll" aria-label="Loading..." role="status">
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
</div>
<script>
    const tableManager = () => ({
        showModal: false,
        editLoc(data) {
            this.showModal = true;
            @this.set('selectedMaterial', data)
        },
        closeModal() {
            this.showModal = false
            @this.selectedMaterial = null
        },
        changeLocation() {
            @this.call('changeLocation', this.$refs.location.value)
            Swal.fire({
                timer: 1500,
                title: 'Change Location Success',
                icon: 'success',
                showConfirmButton: false,
                timerProgressBar: true,
            });
            this.showModal = false
        }
    })
    const clearData = () => {
        Swal.fire({
            title: 'Reset This Data ?',
            icon: 'warning',
            showConfirmButton: true,
            showDenyButton: true,
        }).then((res) => {
            if (res.isConfirmed) {
                @this.call('clearData')
            }
        });
    }
    const saveAll = () =>{
        Swal.fire({
            title: 'Save change data?',
            icon: 'warning',
            showConfirmButton: true,
            showDenyButton: true,
        }).then((res) => {
            if (res.isConfirmed) {
                @this.call('saveAll')
            }
        });

    }
</script>

</div>
@script
    <script>
        $wire.on('notification', (prop) => {
            Swal.fire({
                timer: prop[0].time,
                title: prop[0].title,
                icon: prop[0].icon,
                showConfirmButton: false,
                timerProgressBar: true,
            });
        })
    </script>
@endscript
