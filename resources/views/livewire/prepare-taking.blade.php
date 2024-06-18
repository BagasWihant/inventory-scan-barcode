<div class="dark:text-white max-w-7xl mx-auto">
    <div class="text-2xl font-extrabold pt-6 pb-1 text-center">Prepare Stock Taking</div>

    <div class="mx-auto max-w-xl">
        {{-- <div class="mb-2 text-center">
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status
                Active</label>
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model="statusActive" wire:click="changeStatusActive" class="sr-only peer">
                <div
                    class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                </div>
            </label>
        </div> --}}

        <div class="mb-5" wire:ignore>
            <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date
                disable</label>
            <input type="date" id="date" wire:model="date" onfocus="this.showPicker()"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                required />
        </div>

        <div class="mb-4" wire:ignore>

            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">User
            </label>
            <select id="listUser" multiple="multiple" style="width: 100%"
                class="!w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                @foreach ($listUser as $p)
                    <option value="{{ $p->id }}">{{ $p->username }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-2">

            <button type="button" wire:click="lock"
                class="text-white block bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">Lock</button>
            <button type="button" wire:click="open"
                class="text-white block bg-amber-700 hover:bg-amber-800 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-800">Open</button>
        </div>
    </div>
    {{-- {{ $listUser }} --}}
</div>

<script>
    const addSelectAll = matches => {
        if (matches.length > 0) {
            return [{
                    id: 'selectAll',
                    text: 'Select all user',
                    matchIds: matches.map(match => match.id)
                },
                ...matches
            ];
        }
    };
    const handleSelection = event => {
        if (event.params.data.id === 'selectAll') {
            $('#listUser').val(event.params.data.matchIds);
            $('#listUser').trigger('change');
            @this.userSelected = event.params.data.matchIds

        };
        // if (event.params.data.id === 'selectAll') {
        //     console.log(event.params.data.matchIds);
        //     curSelIds = $('#listUser').val() || [];
        //     $('#listUser').val([...curSelIds, ...event.params.data.matchIds]);
        //     $('#listUser').trigger('change');
        // }

    };
    $("#listUser").select2({
        placeholder: "Select User",
        multiple: true,
        allowClear: true,
        sorter: addSelectAll,
        width: 'resolve',
    })

    $('#listUser').on('select2:select', handleSelection);
</script>