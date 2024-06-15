<div class="dark:text-white max-w-7xl mx-auto">

    <div class="text-2xl font-extrabold py-6 text-center">Abnormal Material</div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <div class="flex justify-between">
            <div class="flex gap-2 mb-2">
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
                <select id="countries" wire:model="status" wire:change="statusChange"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="-">Choose Status</option>
                    <option value="0">Kurang</option>
                    <option value="1">Kelebihan</option>
                </select>

            </div>
            <div class="">
                <button type="button" wire:click="exportExcel"
                    class="text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Excel</button>
                <button type="button" wire:click="exportPdf"
                    class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">PDF</button>
            </div>
        </div>

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Palet No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Material No
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
                            {{ $d->material_no }}
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
                            <button type="button" wire:click="konfirmasi(`{{ $d->pallet_no.'|'.$d->material_no }}`)"
                                class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">Konfirmasi</button>
                            <button type="button"
                                class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">Kembalikan</button>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@script
    <script>
        $wire.on('searchFocus', (event) => {
            $("#search").focus()
        });
        $wire.on('modalConfirm', async (event) => {
            const {
                value: qty
            } = await Swal.fire({
                title: "Save to Warehouse",
                input: "number",
                inputValue: event[0] ?? 0,
                inputLabel: "Qty per pax",
                inputPlaceholder: "qty",
                showDenyButton: true,
                denyButtonText: `Don't save`
            }).then((result) => {
                if(result.value > event[0]){
                    return Swal.fire({
                        timer: 1000,
                        title: "Qty Tidak sesuai",
                        icon: "error",
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                }
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $wire.dispatch('konfirmasi', {
                        qty: result.value,
                    })
                    Swal.fire({
                        timer: 1000,
                        title: "Saved",
                        icon: "success",
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
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
    </script>
@endscript
