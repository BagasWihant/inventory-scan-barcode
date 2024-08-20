<div>

    <div class="text-2xl font-extrabold py-6 text-center">Create New Palet</div>
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-start">
            <a wire:navigate href="{{ route('register_palet') }}"
                class=" text-base text-white block bg-amber-700 font-bold rounded-lg px-2 py-1 text-center ">Back</a>
        </div>

        <div class="flex flex-col justify-self-center justify-center my-3">
            <strong>Palet No : {{ $palet_no }} </strong>
            <strong>Line CD : {{ $lineSelected }}</strong>
        </div>

        <div class="w-full flex gap-6 my-3">
            <div class=" w-full" wire:ignore>
                <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Line CD
                </label>
                <select id="lineselect" style="width: 100%"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full !p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected>Choose Line C</option>
                    @foreach ($listLocation as $p)
                        <option value="{{ $p->location_cd }}">{{ $p->location_cd }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full">
                <label for="large-input"  class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Scan
                    Material
                </label>
                <input wire:model="scanMaterial" wire:keydown.debounce.150ms="scanMaterialChange" type="text" id="scanMaterial"
                    class=" w-full p-2 text-gray-900 border border-gray-300 rounded-lg text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
        </div>

        @if (count($data) > 0)
            <h2 class="p-3 text-xl text-center font-extrabold dark:text-white">List Material </h2>
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Material No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Material Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Qty
                        </th>
                        <th scope="col" class="px-6 py-3">

                        </th>
                    </tr>
                </thead>
                <tbody class="bg-green-50">
                    @foreach ($data as $product)
                        <tr class=" border rounded dark:border-gray-700 ">
                            <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->material_no }}</th>

                            <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->material_name }}
                            </th>
                            <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->qty }}
                            </th>
                            <th scope="row" class="p-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <button class="bg-red-500  text-white font-bold py-2 px-4 rounded-lg"
                                    wire:click="deleteMaterial({{ $product->id }})">Delete</button>
                            </th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex justify-end my-3">
                <button class="bg-green-500  text-white font-bold py-2 px-4 rounded-lg" wire:click="savePallet">Save
                    Pallet</button>
            </div>

        @endif
    </div>

</div>
@script
    <script>
        $(document).ready(function() {

            $('#lineselect').select2({
                width: 'resolve',
                tags: true
            });
            $('#lineselect').on('select2:select', function(e) {
                @this.set('lineSelected', e.params.data.id)
            });
            $wire.on('addMaterial', (data) => {
                Swal.fire({
                    title: "Add Material",
                    html: `
                            <div class="flex flex-col">
                                <strong>Scanned Material</strong>
                                <input id="swal-input1" class="swal2-input" value="${data[0].material_no}" readonly >
                                <strong>Material Name</strong>
                                <input id="swal-input2" class="swal2-input" value="${data[0].material_name}" readonly>
                            </div>
                            <div class="flex flex-col">
                                <strong>Qty</strong>
                                <input id="qty" type="number" max="${data[0].max}"  class="swal2-input" >
                            </div>`,
                    showDenyButton: true,
                    denyButtonText: `Don't save`,
                    preConfirm: () => {
                        return [
                            document.getElementById("qty").value
                        ];
                    },
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        if (parseInt(result.value[0]) > parseInt(data[0].max)) {
                            @this.scanMaterial = null
                            return Swal.fire({
                                timer: 1000,
                                title: "Max QTY is " + data[0].max,
                                icon: "info",
                                showConfirmButton: false,
                                timerProgressBar: true,
                            });
                        }
                        $wire.dispatch('savingMaterial', {
                            qty: result.value[0],
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
            })
            $wire.on('notification', (event) => {
                Swal.fire({
                    timer: 1000,
                    title: event[0].title,
                    icon: event[0].icon,
                    showConfirmButton: false,
                    timerProgressBar: true,
                })
            })
        });
    </script>
@endscript
