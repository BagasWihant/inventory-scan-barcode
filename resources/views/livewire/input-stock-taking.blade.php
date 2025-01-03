<div class="dark:text-white max-w-7xl mx-auto">
    <div wire:loading.flex
        class=" fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
        wire:target="materialCode" aria-label="Loading..." role="status">
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

    <div class="text-2xl font-extrabold py-6 text-center">Input Stock Taking</div>

    <div class="flex justify-end" wire:ignore>
        <button type="button" id="hideForm" style="display: none"
            class="text-white bg-gradient-to-r from-red-500 to-pink-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
            Hide Input
        </button>
        <button type="button" id="showForm"
            class="text-white bg-gradient-to-r from-blue-500 to-teal-400 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
            Show Input
        </button>

    </div>

    <div id="tambahForm" style="display: none" wire:ignore.self>
        <div class="grid grid-cols-2 gap-5">
            <div class="w-full " wire:ignore>
                <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hitung ke
                </label>
                {{-- <select wire:model="hitung"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full !p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option>Choose</option>
                @for ($index = 1; $index <= 3; $index++)
                    <option value="{{ $index }}">{{ $index }}</option>
                @endfor
            </select> --}}
                <ul
                    class="items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="horizontal-list-radio-license" type="radio" value="1" wire:model="hitung"
                                name="hitung"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="horizontal-list-radio-license"
                                class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">1 </label>
                        </div>
                    </li>
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="horizontal-list-radio-id" type="radio" value="2" wire:model="hitung"
                                name="hitung"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="horizontal-list-radio-id"
                                class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">2</label>
                        </div>
                    </li>
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="horizontal-list-radio-military" type="radio" value="3"
                                wire:model="hitung" name="hitung"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="horizontal-list-radio-military"
                                class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">3</label>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="w-full">
                <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Palet
                    Code
                </label>
                <input type="text" id="lokasi" wire:model.live="materialCode"
                    class="mb-6 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500">

                <div class="absolute -mt-4">
                    <div class="py-3 text-center bg-green-100 text-green-700 rounded-lg" wire:loading.block
                        wire:target="poChange">Searching</div>
                    <div wire:loading.remove class="rounded-lg bg-slate-50 shadow">

                        @if (strlen($materialCode) >= 3 && $showSearch)
                            @forelse ($listMaterial as $p)
                                <div class="py-1 px-3 text-base hover:bg-blue-200 rounded-lg" role="button"
                                    wire:click="$set('materialCode', '{{ $p }}')">{{ $p }}
                                </div>
                            @empty
                                <div class="py-3 text-center text-base bg-red-200 rounded-lg">Tidak Ditemukan</div>
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label for="first_name"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Location</label>
                <input type="text" id="lokasi" wire:model="location"
                    class="mb-6 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div>
                <label for="first_name"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Qty</label>
                <input type="text" id="qty" wire:model="qty"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Qty" />
                <input type="text" id="ii" hidden>
            </div>
        </div>

        <div class="flex justify-end gap-5">
            @if ($materialCode)
                <button type="button" wire:click="save"
                    class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
                    {{ $update ? 'Update' : 'Save' }}
                </button>
            @endif
            <button type="button" wire:click="cancel"
                class="text-white bg-gradient-to-r from-red-500 to-pink-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">Cancel</button>

        </div>
    </div>


    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    STO ID
                </th>
                <th scope="col" class="px-6 py-3">
                    <div class="flex items-center">
                        Material No
                    </div>
                </th>
                <th scope="col" class="px-6 py-3">
                    <div class="flex items-center">
                        Hitung
                    </div>
                </th>
                <th scope="col" class="px-6 py-3">
                    <div class="flex items-center">
                        Location
                    </div>
                </th>
                <th scope="col" class="px-6 py-3">
                    <div class="flex items-center">
                        Qty
                    </div>
                </th>
                <th scope="col" class="px-6 py-3">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $d->sto_id }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $d->material_no }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->hitung }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->loc }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $d->qty }}
                    </td>
                    <td>
                        <button @click="editBtn($event.target.getAttribute('prop'))" prop="{{ json_encode($d) }}"
                            class="text-white bg-gradient-to-r from-blue-500 to-teal-400 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-xl text-sm p-2.5 text-center me-2 mb-2">Edit</button>
                        <button x-on:click="$wire.delBtn({{ $d->id }})" wire:loading.attr="disabled"
                            wire:loading.class="cursor-not-allowed" wire:loading.class.remove="from-red-500"
                            wire:target="delBtn"
                            class="text-white bg-gradient-to-r from-red-500 to-pink-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-xl text-sm p-2.5 text-center me-2 mb-2">Delete</button>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function editBtn(params) {
        data = JSON.parse(params)

        @this.set('materialCode', data.material_no).then(() => {
            @this.set('location', data.loc);
            @this.set('qty', data.qty);
            @this.set('hitung', data.hitung);
            @this.set('update', true);
            @this.set('idStockTaking', data.id);
            $('#showForm').hide()
            $('#hideForm').show()
            $('#tambahForm').slideDown(600)

        })


    };
</script>
@script
    <script>
        $(document).ready(function() {

            $wire.on('qtyFocus', (event) => {
                setTimeout(function() {
                    $("#qty").focus()
                }, 50);
            });
            $wire.on('notification', (event) => {
                Swal.fire({
                    timer: 2000,
                    title: event[0].title,
                    icon: event[0].icon,
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
                $('#materialselect').val(null).trigger('change');
            });
            $wire.on('reset', (event) => {
                $('#materialselect').val(null).trigger('change');
                $('#ii').val('');

            });

            $wire.on('popup', (event) => {
                if (event[0].id) {
                    Swal.fire({
                        title: 'Do you want to delete this data?',
                        confirmButtonText: 'Yes, delete it!',
                        showDenyButton: true,
                        denyButtonText: `No, cancel`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $wire.dispatch('deleteMat', {
                                id: event[0].id
                            })
                            Swal.fire({
                                timer: 1000,
                                title: "deleted",
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
                } else {
                    Swal.fire({
                        timer: 1000,
                        title: event[0].title,
                        icon: "error",
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });
                }

            })

            $('#showForm').on('click', function() {
                $('#showForm').hide()
                $('#hideForm').show()
                $('#tambahForm').slideDown(600)

            });

            $('#hideForm').on('click', function() {
                $('#showForm').show()
                $('#hideForm').hide()
                $('#tambahForm').slideUp(600)
            });

        });
    </script>
@endscript
