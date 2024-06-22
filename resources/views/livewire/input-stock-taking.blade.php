<div class="dark:text-white max-w-7xl mx-auto">
    <div class="text-2xl font-extrabold py-6 text-center">Input Stock Taking</div>

    <div class="flex justify-end">
        <button type="button" id="hideForm" style="display: none" 
            class="text-white bg-gradient-to-r from-red-500 to-pink-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
            Hide Input
        </button>
        <button type="button" id="showForm" 
            class="text-white bg-gradient-to-r from-green-500 to-teal-400 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">
            Show Input
        </button>

    </div>

    <div id="tambahForm" style="display: none" wire:ignore>
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
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="horizontal-list-radio-license"
                                class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">1 </label>
                        </div>
                    </li>
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="horizontal-list-radio-id" type="radio" value="2" wire:model="hitung"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="horizontal-list-radio-id"
                                class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">2</label>
                        </div>
                    </li>
                    <li class="w-full border-b border-gray-200 sm:border-b-0 sm:border-r dark:border-gray-600">
                        <div class="flex items-center ps-3">
                            <input id="horizontal-list-radio-military" type="radio" value="3" wire:model="hitung"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                            <label for="horizontal-list-radio-military"
                                class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">3</label>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="w-full" wire:ignore>
                <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Palet Code
                </label>
                <select id="materialselect" style="width: 100%"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full !p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected>Choose Material</option>
                    @foreach ($listMaterial as $p)
                        <option value="{{ $p }}">{{ $p }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label for="first_name"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Location</label>
                <input type="text" id="disabled-input" wire:model="location"
                    class="mb-6 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div>
                <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Qty</label>
                <input type="text" id="first_name" wire:model="qty"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Qty" />
            </div>
        </div>

        <div class="flex justify-end gap-5">
            @if ($materialCode)
                <button type="button" wire:click="save"
                    class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">Save</button>
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
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


@script
    <script>
        $(document).ready(function() {
            $('#materialselect').select2({
                placeholder: "Material Code",
                width: 'resolve'
            });
            $('#materialselect').on('change', function(e) {
                @this.materialCode = e.target.value
                $wire.dispatch('materialChange')
            });
            $wire.on('reset', (event) => {
                $('#materialselect').val(null).trigger('change');
            });

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
            // function showForm() {
            //     $('#showForm').css('display', 'none')
            //     $('#hideForm').css('display', 'block')
            // }
            // function hideForm() {
            //     $('#showForm').css('display', 'block')
            //     $('#hideForm').css('display', 'none')
            // }


        });
    </script>
@endscript
