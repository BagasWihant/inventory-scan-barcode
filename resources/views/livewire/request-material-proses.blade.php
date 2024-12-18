<div class="max-w-7xl mx-auto">
    <div class="text-2xl text-center font-extrabold py-6" id="setHerePagination">Proses Material Request</div>

    <div class="relative w-full">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
        </div>
        <input type="text" id="search" wire:model.live.debounce.300ms="searchKey"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="Transaksi No." />
    </div>

    <div class="relative overflow-x-auto shadow-md rounded-lg my-4">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-gray-900 uppercase bg-gray-300">
                <tr>
                    <th scope="col" class="px-1 py-3">
                        Transaksi No
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Notes
                    </th>
                    <th scope="col" class="px-6 py-3">

                    </th>
                    <th scope="col" class="px-6 py-3">
                        Form
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                    <tr class="py-2 @if ($d->type == 2) bg-red-700 text-white font-semibold hover:bg-red-800 @endif hover:bg-gray-200">
                        <td class="px-2" role="button">
                            {{ $d->transaksi_no }}
                        </td>
                        <td role="button">
                            {{ $d->type == 2 ? 'Urgent' : 'Reguler' }}
                        </td>
                        <td></td>
                        <td>
                            <button class="bg-blue-600 px-4 py-2 text-white rounded-md" wire:click="print('{{ $d->transaksi_no }}')">Print</button>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
