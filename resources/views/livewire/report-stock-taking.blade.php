<div class="dark:text-white max-w-7xl mx-auto">
       
    <div class="fixed left-0 top-0  h-screen w-screen flex justify-center items-center bg-slate-300/70 z-50"
        wire:loading.flex wire:target="showData,export">
        <div class="absolute animate-spin rounded-full h-32 w-32 border-t-4 border-b-4 border-purple-500"></div>
        <img src="https://www.svgrepo.com/show/509001/avatar-thinking-9.svg" class="rounded-full h-28 w-28">
    </div>

    <div class="text-2xl font-extrabold pt-6 text-center">Report Stock Taking </div>
    @if ($stoId)
        <div class="text font-sans py-1 text-center"><strong> {{ 'STO ID : ' . $stoId }}</strong> </div>
    @endif
    <div class="text font-sans pb-3 text-center">Date : {{ date('d-m-Y') }} </div>


    <div class="flex justify-between pb-3">
        <div class="col-span-2" wire:ignore>
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            </label>
            <select wire:model="stoId"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full !p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">Choose STO ID</option>
                @foreach ($listSto as $p)
                    <option value="{{ $p->id }}">{{ $p->id }} |
                        {{ date('d-m-Y H:i:s', strtotime($p->date_start)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-1 flex justify-center items-end gap-4">
            @if (count($data) > 0)
                <button wire:click="export"
                    class="shadow-lg relative inline-flex items-center justify-center  overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-green-500 to-cyan-500 group-hover:from-green-500 group-hover:to-cyan-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">
                    <span
                        class="relative px-5 py-1 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                        Export
                    </span>
                </button>
            @endif
            <button wire:click="showData"
                class="shadow-lg relative inline-flex items-center justify-center  overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-cyan-500 to-blue-500 group-hover:from-cyan-500 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">
                <span
                    class="relative px-5 py-1 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                    Show Data
                </span>
            </button>
        </div>
    </div>

    @if (count($data) > 0)

        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 shadow-lg">
            <thead
                class="text-xs border border-gray-300 text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr class=" ">
                    <th scope="col" class="px-6 text-center py-3 border border-gray-300" rowspan="2">
                        Material No
                    </th>
                    <th scope="col" class="text-center py-1 border border-gray-300" colspan="2">
                        System
                    </th>

                    <th scope="col" class="text-center py-1 border border-gray-300" colspan="2">
                        STO
                    </th>
                    <th scope="col" class="px-6 text-center py-3 border border-gray-300" colspan="2">
                        Result
                    </th>
                </tr>
                <tr>
                    <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Loc s
                    </th>
                    <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Qty s
                    </th>
                    <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Loc
                    </th>
                    <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        Qty
                    </th>
                    <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        +
                    </th>
                    <th scope="col" class="px-6 py-3 border border-gray-300 text-center">
                        -
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $d->material_no }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $d->loc_sys ?? ' ' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->qty_sys ?? ' ' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->loc_sto ?? ' ' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->qty_sto ?? ' ' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->result_qty > 0 ? $d->result_qty : ' ' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $d->result_qty < 0 ? abs($d->result_qty) : ' ' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
