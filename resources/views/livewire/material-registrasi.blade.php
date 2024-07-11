<div class="darkmax-w-7xl mx-auto">
    <div class="text-2xl text-center font-extrabold py-6" id="setHerePagination">Material Registrasi</div>

    <div class="flex gap-5 justify-between">

        <div class="w-1/4" wire:ignore>
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Material No
            </label>

            <select id="materialselect" style="width: 100%" wire:model="material"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full !p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option selected>Choose Material</option>
                @foreach ($listMaterialComboBox as $p)
                    <option value="{{ $p->matl_no }}">{{ $p->matl_no }}</option>
                @endforeach
            </select>

            {{-- <input wire:model="searchMaterial" wire:keydown.debounce.300ms="materialType" type="text" id="produkBarcode"
            @if ($materialDisable) disabled @endif autocomplete="off"
            class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <div class="absolute">
                <div class="py-3 text-center bg-green-100 text-green-700 rounded-lg" wire:loading.block
                    wire:target="materialType">Searching</div>
                <div wire:loading.remove class="rounded-lg bg-slate-50 shadow overflow-y-auto h-72">

                    @if (strlen($searchMaterial) >= 1 && $material != $searchMaterial)
                        @forelse ($listMaterialComboBox as $p)
                            <div class="py-1 px-3 text-base hover:bg-blue-200 rounded-lg" role="button"
                                wire:click="chooseMaterial('{{ $p->matl_no }}')">{{ $p->matl_no }}
                            </div>
                        @empty
                            <div class="py-3 text-center text-base bg-red-200 rounded-lg">Tidak Ditemukan</div>
                        @endforelse
                    @endif
                </div>
            </div> --}}

        </div>
        <div class="flex items-center">
            <button type="button" wire:click="addMaterial"
                class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-xl text-sm px-5 py-4 text-center">
                Tambah</button>
        </div>
    </div>

    @if ($listMaterialAdded != null)
        <div class="text-base text-left font-extrabold py-6" id="setHerePagination">List Material Registrasi</div>

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-2 py-1">
                        <div class="flex items-center">
                            No
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-1">
                        <div class="flex items-center">
                            Material No
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-1">
                        <div class="flex items-center">
                            Action
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                {{-- {{ dd($listMaterialAdded) }} --}}
                @foreach ($listMaterialAdded as $d)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-1 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $loop->iteration }}
                        </th>
                        <td class="px-6 py-1 text-base  text-black">
                            {{ $d->material_no }}
                        </td>
                        <td class="px-6 py-1">
                            <button type="button" id="btn-kembalikan" onclick="hapus({{ $d->id }})"
                                class="text-white bg-gradient-to-r from-red-500  to-red-600 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none transition-all focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-xl text-sm px-5 py-2.5 text-center me-2 mb-2">Hapus</button>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $listMaterialAdded->links() }}
        <script>
            function hapus(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.deleteMaterial(id)
                    }
                })
            }
        </script>
    @endif
</div>

@script
    <script>
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
        $('#materialselect').select2({
            placeholder: "Material Code",
            width: 'resolve'
        });
        $('#materialselect').on('change', function(e) {
            @this.material = e.target.value
            // id = $('#ii').val();
            // $('#ii').val('');
        });
    </script>
@endscript
