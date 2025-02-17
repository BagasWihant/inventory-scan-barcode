<div class="dark:text-white max-w-7xl mx-auto">

    <div class="text-2xl font-extrabold py-6 text-center">Abnormal Material</div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <div class="flex justify-between">
            <div class="flex gap-2 mb-2 w-2/3">
                <label for="simple-search" class="sr-only">Search</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" id="search" wire:model.live.debounce="searchKey"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Search here..." required />
                </div>
                <input type="date" id="dtstart" wire:model.live="dateFilter" onfocus="this.showPicker()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" >
                <select wire:model="status" wire:change="statusChange"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="-">Choose Status</option>
                    <option value="00">Kurang</option>
                    <option value="1">Kelebihan</option>
                </select>
                <select wire:model="location" wire:change="locationChange"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="-">Semua Location</option>
                    <option value="ASSY">ASSY</option>
                    <option value="CNC">CNC</option>
                    <option value="other">Other</option>
                </select>

            </div>
            {{-- <div class="">
                <button type="button" wire:click="exportExcel"
                    class="text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Excel</button>
                <button type="button" wire:click="exportPdf"
                    class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">PDF</button>
            </div> --}}
        </div>

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Palet No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Kit No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Material No
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Line C
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Pax
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Qty
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Trucking ID
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Location
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Status
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Created At
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Aksi
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row"
                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $d->pallet_no }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $d->kit_no }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->material_no }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->line_c }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->pax }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->qty }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->trucking_id }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->locate }}
                    </td>
                    <td class="px-6 py-4">
                        @if ($d->status == 0)
                        KURANG
                        @elseif ($d->status == 1)
                        KELEBIHAN
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($d->created_at)->format('d-m-Y') }}
                    </td>
                    <td class="flex">
                        <button type="button"
                            wire:click="konfirmasi(`{{ $d->pallet_no . '|' . $d->material_no }}`)"
                            class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">Konfirmasi</button>
                        <button type="button" id="btn-kembalikan"
                            data-nya="{{ $d->pallet_no . '|' . $d->material_no }}"
                            class="text-white bg-gradient-to-r from-red-500  to-red-600 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">Kembalikan</button>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">
            {{ $data->links() }}
        </div>
    </div>
    <div wire:loading.flex
        class=" fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        wire:target="kembalikan,savingToStock,konfirmasi" aria-label="Loading..." role="status">
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

@script
<script>
    $('#btn-kembalikan').click(function() {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                let data = $(this).data('nya');

                $wire.dispatch('kembalikan', {
                    req: data
                })
            }
        });
    });
    $wire.on('searchFocus', (event) => {
        $("#search").focus()
    });
    $wire.on('notif', (event) => {
        Swal.fire({
            timer: 1000,
            title: event[0].title,
            icon: event[0].icon,
            showConfirmButton: false,
            timerProgressBar: true,
        });
    });
    $wire.on('modalConfirm', async (event) => {

        const inputQty = `<div class="flex flex-col">
                    <label for="qty" class=" text-left text-gray-900">Total Qty</label>
                    <input type="number" readonly id="qty" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="qty">
                </div>`
        const htmlVal = `
                <b>Total ${event[0].pax} pax</b><br>
                ${inputQty}
                <div class="flex flex-col">
                    <label for="lineC" class=" text-left text-gray-900">Line C</label>
                    <input value="${event[0].line}" type="text" id="lineC" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Line C">
                </div>
                <div class="flex flex-col">
                    <label for="dateIssue" class=" text-left text-gray-900">Issue Date</label>
                    <input placeholder="dd-mm-yyyy" type="date" value="${event[0].date}" id="dateIssue" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Line C">
                </div>
                
                `


        await Swal.fire({
            title: "Save to Warehouse",
            html: `${htmlVal}`,
            // inputValue: event[0].qty ?? 0,
            // inputLabel: "Qty per pax",
            // inputPlaceholder: "qty",
            showDenyButton: true,
            denyButtonText: `Don 't save`,
            didOpen: function() {
                $('#qty').val(event[0].qty)
                event[0].line && $('#lineC').val(event[0].line)
            },
            preConfirm: () => {
                return [
                    document.getElementById("qty").value,
                    document.getElementById("lineC").value,
                    document.getElementById("dateIssue").value

                ];
            }
        }).then((result) => {
            qty = parseInt(result.value[0])
            lineC = result.value[1]
            date = result.value[2]


            if (qty > event[0].qty) {
                return Swal.fire({
                    timer: 2200,
                    html: `
                        Max Qty is <b> ${event[0].qty} </b> your input is <b> ${qty} </b>`,
                    title: "Invalid Input",
                    icon: "error",
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            }

            if (result.isConfirmed) {

                data = {
                    pax: event[0].pax,
                    lineC: lineC,
                    qty: qty,
                    pallet_no: event[0].pallet_no,
                    material_no: event[0].material_no,
                    issue_date: date
                }
                $wire.dispatch('savingToStock', {
                    req: data
                })
            } else if (result.isDenied) {
                // $wire.dispatch('insertNew', {
                //     save: false
                // })
                Swal.fire({
                    timer: 1000,
                    title: "Changes are not saved",
                    icon: "info",
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
            }
        });
    });

    $wire.on('palletInput', (event) => {

        const palet = `<div class="flex flex-col">
                    <label for="modal_paletno" class=" text-left text-gray-900">Pallet No</label>
                    <input type="text" maxlength="10" id="modal_paletno" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Pallet No">
                </div>`
        const htmlVal = `${palet}`
        Swal.fire({
            title: "Save to Warehouse",
            html: `${htmlVal}`,
            showDenyButton: true,
            denyButtonText: `Don 't save`,
            didOpen: function() {
                $('#modal_paletno').val(event[0].pallet_no)
            },
            preConfirm: () => {
                return [
                    document.getElementById("modal_paletno").value,
                ];
            }
        }).then((result) => {
            let paletno = result.value[0]



            if (result.isConfirmed) {
                if (paletno == '' || paletno == null) {
                    return Swal.fire({
                        timer: 2200,
                        html: `
                           Mohon isi <b> Pallet No </b>`,
                        title: "Invalid Input",
                        icon: "error",
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                }

                data = {
                    pax: event[0].pax,
                    lineC: event[0].line,
                    pallet_no: event[0].pallet_no,
                    material_no: event[0].material_no,
                    palletNo_new: paletno
                }
                $wire.dispatch('savingToStock', {
                    req: data
                })
            } else if (result.isDenied) {
                Swal.fire({
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