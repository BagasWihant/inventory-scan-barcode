<div>
    <div class="flex gap-4">
        <div class="flex flex-col w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Palet BARCODE
            </label>

            <span
                class="@if (!$paletInput) hidden @endif font-medium text-gray-900 dark:text-white border border-gray-300 p-4 rounded-md bg-gray-200">
                {{ $paletBarcode }} </span>

            <input focus wire:model="paletBarcode" wire:keydown.debounce.150ms="paletBarcodeScan" type="text"
                id="paletBarcode" @if ($paletInput) hidden @endif
                class=" w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

        </div>
        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">TRUCKING ID
            </label>
            <input wire:model="trucking_id" type="text"
                class="block w-full p-4 text-gray-700 border border-gray-300 rounded-lg bg-gray-200 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                readonly>
        </div>

        <div class="w-full">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PRODUK BARCODE
            </label>
            <input wire:model="produkBarcode" wire:keydown.debounce.150ms="productBarcodeScan" type="text"
                id="produkBarcode"
                class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>
    </div>

    <div wire:loading.flex
        class=" fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        wire:target="confirm,productBarcodeScan,paletBarcodeScan,resetPage" aria-label="Loading..." role="status">
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

    @if (count($productsInPalet) > 0)
        <div class="grid grid-cols-2 row gap-4 overflow-x-auto sm:rounded-lg p-3 ">
            <div class="w-full">

                <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">List Barang </h2>
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Material No
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Line Code
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Pax
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Qty pickinglist
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productsInPalet as $product)
                            <tr class=" border rounded dark:border-gray-700 ">
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white h-5">
                                    {{ $product->material_no }}</th> 
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $product->line_c }}</th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if ($product->serial_no == '00000')
                                        0
                                    @else
                                        {{ $product->pax }}
                                    @endif
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if ($product->serial_no == '00000')
                                        0
                                    @else
                                        {{ $product->picking_qty }}
                                    @endif
                                </th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- {{ $productsInPalet->links() }} --}}

            </div>

            <div class="w-full">
                <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">Berhasil di Scan</h2>
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col " class="px-6 py-3">
                                Material No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                <div class="flex items-center">
                                    Qty received
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Location
                            </th>
                            <th scope="col" class="w-4">
                                keterangan
                            </th>
                            <th scope="col" class="px-3 py-3">
                                <div class="flex items-center">
                                    Action
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($scanned as $v)
                            @php
                                if ($v->total == $v->counter && $v->qty_more == 0) {
                                    $ket = 'OK CONFIRM';
                                    $class = ' bg-green-300 dark:bg-green-500';
                                } elseif ($v->counter > $v->total || $v->qty_more > 0) {
                                    $ket = 'EXCESS';
                                    $class = ' bg-amber-400';
                                } else {
                                    $ket = 'OUTSTANDING / NOT CLOSE';
                                    $class = ' bg-red-300 dark:bg-red-500';
                                }
                            @endphp
                            <tr class=" border rounded {{ $class }} dark:border-gray-700">
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white h-5">
                                    {{ $v->material }}</th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->counter }} </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $v->location_cd }}
                                </th>
                                <th scope="row"
                                    class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $ket }}
                                </th>
                                <th scope="row"
                                    class="p-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if ($v->counter > 0)
                                        <div class="" x-data="{
                                            openModalQty(data) {
                                                console.log(data[1]);
                                                Swal.fire({
                                                    title: `Edit qty ${data[1]}`,
                                                    input: 'number',
                                                    inputValue: data[0],
                                                    inputLabel: 'Qty ',
                                                    inputPlaceholder: 'qty',
                                                    showDenyButton: true,
                                                    denyButtonText: `Don't save`
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $dispatch('editQty', {
                                                            qty: result.value,
                                                            id: data[1]
                                                        })
                                                    } else if (result.isDenied) {
                                                        return Swal.fire({
                                                            timer: 1000,
                                                            title: 'Changes are not saved',
                                                            icon: 'info',
                                                            showConfirmButton: false,
                                                            timerProgressBar: true,
                                                        });
                                                    }
                                                });
                                            }
                                        }">

                                            <button
                                                wire:click="resetItem({{ json_encode([$v->material, $v->palet,$v->id]) }})"
                                                class="relative inline-flex items-center justify-center  overflow-hidden text-sm font-medium text-gray-900 rounded-lg p-1 group bg-gradient-to-br from-red-800 to-red-500 group-hover:from-red-900 group-hover:to-red-600 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">
                                                Reset
                                            </button>
                                            <button @click="openModalQty(@js([$v->counter, $v->id]))"
                                                class="relative inline-flex items-center justify-center  overflow-hidden text-sm font-medium text-gray-900 rounded-lg p-1 group bg-gradient-to-br from-yellow-800 to-yellow-500 group-hover:from-yellow-900 group-hover:to-yellow-600 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">
                                                Edit
                                            </button>
                                        </div>
                                    @endif
                                </th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- {{ $scanned->links() }} --}}
            </div>

        </div>
        <div class="flex justify-end pt-3">
            <button type="button" wire:click="resetPage"
                class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Reset</button>
            <button type="button" wire:click="confirm"
                class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
                <span wire:loading.remove>
                    Download & Konfimasi
                </span>
                <div role="status" wire:loading wire:target="confirm">
                    <svg aria-hidden="true"
                        class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-gray-600 dark:fill-gray-300"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill" />
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
            </button>
        </div>
    @else
        <div class="w-full" wire:loading.remove>
            <h2 class="p-5 text-2xl text-center font-extrabold dark:text-white">{{ $props[1] }} </h2>
            @if ($props[0] == 1)
                <div class="text-center">

                    <button type="button" wire:click="resetPage"
                        class=" text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Rescan</button>
                </div>
            @endif
        </div>
    @endif



</div>
@script
    <script>
        $wire.on('produkFocus', (event) => {
            $("#produkBarcode").focus()
        });
        $wire.on('paletFocus', (event) => {
            setTimeout(function() {
                $("#paletBarcode").val("")
                $("#paletBarcode").focus()
            }, 50);
        });
        $wire.on('newItem', async (event) => {
            // jika item duplicate
            if (event[0].update) {

                await Swal.fire({
                    title: event[0].title,
                    input: "number",
                    inputValue: event[0].qty ?? 0,
                    inputLabel: "Qty ",
                    inputPlaceholder: "qty",
                    showDenyButton: true,
                    denyButtonText: `Don't save`
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        $wire.dispatch('insertNew', {
                            qty: result.value,
                            update: true,
                            save: false
                        })
                        return Swal.fire({
                            timer: 1000,
                            title: "Updated",
                            icon: "success",
                            showConfirmButton: false,
                            timerProgressBar: true,
                        });
                    } else if (result.isDenied) {
                        console.log('here');
                        $wire.dispatch('insertNew', {
                            save: false
                        })
                        return Swal.fire({
                            timer: 1000,
                            title: "Changes are not saved",
                            icon: "info",
                            showConfirmButton: false,
                            timerProgressBar: true,
                        });
                    }
                });
                return
            }

            await Swal.fire({
                title: event[0].title,
                input: "number",
                inputValue: event[0].qty ?? 0,
                inputLabel: "Qty ",
                inputPlaceholder: "qty",
                showDenyButton: true,
                denyButtonText: `Don't save`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $wire.dispatch('insertNew', {
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
                    $wire.dispatch('insertNew', {
                        save: false
                    })
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

        $wire.on('newItem2', (event) => {

            Swal.fire({
                title: event[0].title,
                html: `Qty : ${event[0].qty}`,
                showDenyButton: true,
                denyButtonText: `Don't save`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $wire.dispatch('insertNew', {
                        qty: event[0].qty,
                    })
                    Swal.fire({
                        timer: 1000,
                        title: "Saved",
                        icon: "success",
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                } else if (result.isDenied) {
                    $wire.dispatch('insertNew', {
                        save: false
                    })
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
