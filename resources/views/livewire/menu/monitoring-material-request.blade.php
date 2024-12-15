@section('title', 'Monitoring Material Request')

<div class="max-w-7xl mx-auto">
    <div class="text-2xl font-extrabold text-center">Monitoring Material Request</div>
    <div class="relative overflow-x-auto shadow-md rounded-lg mt-7">
        <table class="w-full text-sm text-left rtl:text-right text-gray-700 ">
            <thead class="text-sm text-gray-900 font-bold uppercase bg-gray-50 ">

                <tr>
                    <th scope="col" class="px-6 py-3 w-[80px]">
                        Transaction No
                    </th>
                    <th scope="col" class="px-6 py-3 w-[50px]">
                        Req. By
                    </th>
                    <th scope="col" class="px-6 py-3 w-[60px]">
                        User
                    </th>
                    <th scope="col" class="px-6 py-3 w-[30px]">
                        Req. Time
                    </th>
                    <th scope="col" class="px-6 py-3 w-[250px]">
                        Material
                    </th>
                    <th scope="col" class="px-6 py-3 w-[30px] text-wrap">
                        Jml. Varian
                    </th>
                    <th scope="col" class="px-6 py-3 w-[100px]">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 w-[100px]">
                        Position
                    </th>
                    <th scope="col" class="px-6 py-3 w-[100px]">
                        Duration
                    </th>
                </tr>
            </thead>
            <tbody>
                <div wire:loading.flex
                    class=" fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
                    wire:target="gotoPage" aria-label="Loading..." role="status">
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
                @foreach ($material as $m)
                    <tr wire:key="material-request-{{ $loop->iteration }}"
                        class="@if ($m->status == 0) bg-red-200  hover:bg-red-300  @else bg-green-200 hover:bg-green-300 @endif border-b">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $m->transaksi_no }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $m->user_request }}
                        </td>
                        <td class="px-6 py-4">
                            {{ strtoupper($m->user->username) }}
                        </td>
                        <td class="px-6 py-4">
                            {{ Carbon\Carbon::parse($m->created_at)->format('H:i:s') }}
                        </td>
                        <td class="px-6 py-4 text-wrap">
                            {{ $m->material_no }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ $m->total_varian }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $m->status == 0 ? 'Not Yet Processed' : 'Processed' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $m->status == 0 ? 'Waiting Prosess' : 'Warehouse' }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($m->proses_date)
                                {{ Carbon\Carbon::parse($m->proses_date)->longAbsoluteDiffForHumans($m->created_at) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $material->links() }}
</div>
