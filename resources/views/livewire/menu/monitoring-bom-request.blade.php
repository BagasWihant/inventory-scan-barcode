@section('title', 'Monitoring Material Request')

<div class="max-w-7xl mx-auto">
    <div class="text-2xl font-extrabold text-center">Monitoring Bom Request</div>
    <div class="flex justify-between my-4">
        <span>
            <p>Refreshed At : <b> {{ date('d-m-Y H:i:s', strtotime($time)) }}</b></p>
        </span>
        <span>
            <p>Total Transaksi Hari ini : <b> {{ $totalCount }}</b></p>
        </span>
    </div>
    <div class="flex my-4 gap-4">
        <div class="flex-col">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Issue Date Filter</label>
            <input id="dtstart" type="date" wire:model.lazy="dateFilter" onclick="this.showPicker()"
                class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Select date">
        </div>
        <div class="flex-col">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Line Code</label>
            <input type="text" wire:model.live.700ms="line_code" 
                class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Line Code">
        </div>
    </div>
    <div class="relative overflow-x-auto shadow-md rounded-lg mt-7">
        <!-- dibuat aagak lama biar gak membebani server -->
        <table wire:poll.60s="refreshTable" class="w-full text-sm text-left rtl:text-right text-gray-700 ">
            <thead class="text-sm text-gray-900 font-bold uppercase bg-gray-50 ">

                <tr>
                    <th scope="col" class="px-6 py-3 w-[80px]">
                        Product No
                    </th>
                    <th scope="col" class="px-6 py-3 w-[80px]">
                        Kit No
                    </th>
                    <th scope="col" class="px-6 py-3 w-[50px]">
                        Line Code
                    </th>
                    <th scope="col" class="px-6 py-3 w-[60px]">
                        Issue Date
                    </th>
                    <th scope="col" class="px-6 py-3 w-[30px]">
                        Entry Date
                    </th>
                </tr>
            </thead>
            <tbody>
                <div wire:loading.flex
                    class=" fixed z-30 bg-slate-900/60 dark:bg-slate-400/35 top-0 left-0 right-0 bottom-0 justify-center items-center h-screen border border-red-800"
                    wire:target="gotoPage" aria-label="Loading..." role="status">
                    <svg class="h-20 w-20 animate-spin stroke-white " viewBox="0 0 256 256">
                        <line x1="128" y1="32" x2="128" y2="64" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="24"></line>
                        <line x1="195.9" y1="60.1" x2="173.3" y2="82.7" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="24"></line>
                        <line x1="224" y1="128" x2="192" y2="128" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="24">
                        </line>
                        <line x1="195.9" y1="195.9" x2="173.3" y2="173.3" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="24"></line>
                        <line x1="128" y1="224" x2="128" y2="192" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="24">
                        </line>
                        <line x1="60.1" y1="195.9" x2="82.7" y2="173.3" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="24"></line>
                        <line x1="32" y1="128" x2="64" y2="128" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="24"></line>
                        <line x1="60.1" y1="60.1" x2="82.7" y2="82.7" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="24">
                        </line>
                    </svg>
                    <span class="text-4xl font-medium text-white">{{ $statusLoading ?? 'Loading...' }}</span>
                </div>
                @foreach ($material as $m)
                    <tbody x-data="{ open: false }" class="border-b">

                        <tr wire:key="material-request-{{ $loop->iteration }}" class="cursor-pointer" @click="open = !open">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $m['product_no'] }}
                            </th>
                            <td class="px-6 py-4">{{ $m['kit_no'] }}</td>
                            <td class="px-6 py-4">{{ strtoupper($m['line_c']) }}</td>
                            <td class="px-6 py-4">{{ $m['plan_issue_dt'] }}</td>
                            <td class="px-6 py-4">{{ $m['entry_dt'] }}</td>
                        </tr>
                        <tr x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96"
                            x-transition:leave-end="opacity-0 max-h-0" style="overflow: hidden;">
                            <td colspan="5" class="px-6 py-4 bg-slate-300/30">
                                <div class="flex  w-full gap-3">
                                    <div class="w-full">
                                        <strong>Request:</strong>
                                        @if (empty($m['detail']))
                                            <p class="text-sm text-gray-500">Tidak ada detail</p>
                                        @else
                                            <table class="text-sm mt-2 w-full">
                                                <thead>
                                                    <tr class="text-left border-b border-black">
                                                        <th class="py-1">Material No</th>
                                                        <th class="py-1">Qty</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($m['detail'] as $d)
                                                        <tr class="border-t border-black">
                                                            <td class="py-1">{{ $d['material_no'] }}</td>
                                                            <td class="py-1">{{ number_format((float) $d['req_bom'], 2, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>

                                    <div class="w-full">
                                        @if (empty($m['instock']))
                                            <p class="text-sm text-gray-500">Tidak ada detail</p>
                                        @else
                                            <strong>Receive:</strong>
                                            <table class="text-sm mt-2 w-full">
                                                <thead>
                                                    <tr class="text-left border-b border-black">
                                                        <th class="py-1">Material No</th>
                                                        <th class="py-1">Qty</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($m['instock'] as $d)
                                                        <tr class="border-t border-black">
                                                            <td class="py-1">{{ $d['material_no'] }}</td>
                                                            <td class="py-1">{{ number_format((float) $d['picking_qty'], 2, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>

                                    <div class="w-full">
                                        @if (empty($m['po_kias']))
                                            <p class="text-sm text-gray-500">Tidak ada detail</p>
                                        @else
                                            <strong>PO: {{ $m['no_po']['no_po'] ?? '-' }}</strong>
                                            <table class="text-sm mt-2 w-full">
                                                <thead>
                                                    <tr class="text-left border-b border-black">
                                                        <th class="py-1">Material No</th>
                                                        <th class="py-1">Qty</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($m['po_kias'] as $d)
                                                        <tr class="border-t border-black">
                                                            <td class="py-1">{{ $d['material_no'] }}</td>
                                                            <td class="py-1">{{ number_format(((float)$d['bom_qty'] * (float)$m['no_po']['qty_request']), 2, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>


                    </tbody>
                @endforeach

            </tbody>
        </table>
    </div>
    <audio id="notificationSound" src="{{ asset('assets/sound2.mp3') }}" preload="auto"></audio>

    <script>
        // Initialize AudioContext on user interaction
        let audioCtx = null;

        document.addEventListener('click', function () {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                console.log("AudioContext initialized");
            }
        }, {
            once: true
        });
    </script>
</div>


@script
<script>
    const notif = document.getElementById('notificationSound');
    $wire.on('playSound', () => {
        notif.play()
    });
    $wire.on('stopSound', () => {
        notif.pause()
        notif.currentTime = 0
    });
</script>
@endscript