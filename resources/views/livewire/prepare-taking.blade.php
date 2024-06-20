<div class="dark:text-white max-w-7xl mx-auto">
    <div class="fixed left-0 top-0  h-screen w-screen flex justify-center items-center bg-slate-300/70 z-50"
        wire:loading.flex wire:target="lock,open,exportPdf">
        <div class="absolute animate-spin rounded-full h-32 w-32 border-t-4 border-b-4 border-purple-500"></div>
        <img src="https://www.svgrepo.com/show/509001/avatar-thinking-9.svg" class="rounded-full h-28 w-28">
    </div>
    <div class="text-2xl font-extrabold py-6 gap-4 text-center flex flex-col">
        <span>
            Prepare Stock Taking
        </span>
        <div class="text-base text-gray-500 text-center">
            Status : @if ($canOpen)
                <span class="text-green-500">
                    {{ 'Active' }}
                </span>
            @else
                <span class="text-red-500">
                    {{ 'NonActive' }}
                </span>
            @endif
        </div>
    </div>
    <div class="mx-auto max-w-md pb-5">
        <div class="grid grid-cols-2 gap-2">

            <button type="button" wire:click="lock" @if ($canOpen) disabled @endif
                class="@if ($canOpen) disabled:opacity-30 @endif text-base text-white block bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-lg  w-full px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Mulai</button>

            <button type="button" wire:click="open" @if (!$canOpen) disabled @endif
                class="@if (!$canOpen) disabled:opacity-30 @endif text-white block bg-amber-700 hover:bg-amber-800 focus:ring-4 focus:outline-none focus:ring-amber-300 font-bold rounded-lg text-base  w-full px-5 py-2.5 text-center dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-800">Batal</button>
        </div>
    </div>

    <div class="">
        <div class="flex justify-end">
            <button type="button" wire:click="exportPdf"
                class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                Cetak
            </button>
        </div>

        @if ($canOpen)
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                No
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Material No
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataStock as $data)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $loop->iteration }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $data->material_no }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>

<script>
    // const addSelectAll = matches => {
    //     if (matches.length > 0) {
    //         return [{
    //                 id: 'selectAll',
    //                 text: 'Select all user',
    //                 matchIds: matches.map(match => match.id)
    //             },
    //             ...matches
    //         ];
    //     }
    // };
    // const handleSelection = event => {
    //     if (event.params.data.id === 'selectAll') {
    //         $('#listUser').val(event.params.data.matchIds);
    //         $('#listUser').trigger('change');
    //         @this.userSelected = event.params.data.matchIds

    //     };
    //     // if (event.params.data.id === 'selectAll') {
    //     //     console.log(event.params.data.matchIds);
    //     //     curSelIds = $('#listUser').val() || [];
    //     //     $('#listUser').val([...curSelIds, ...event.params.data.matchIds]);
    //     //     $('#listUser').trigger('change');
    //     // }

    // };
    // $("#listUser").select2({
    //     placeholder: "Select User",
    //     multiple: true,
    //     allowClear: true,
    //     sorter: addSelectAll,
    //     width: 'resolve',
    // })

    // $('#listUser').on('select2:select', handleSelection);
</script>
