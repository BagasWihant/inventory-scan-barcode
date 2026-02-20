<div class="p-4 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-7">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex gap-3 items-center ">
                @if ($rakInfo)
                    <a wire:navigate href="{{ route('rak.detail', ['rak_id' => $rakId]) }}"
                        class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-2xl shadow-sm hover:bg-slate-50 transition text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <span
                        class="text-xl md:text-2xl font-black tracking-tight text-slate-800 leading-tight uppercase font-mono">
                        History Transaksi - {{ strtoupper($rakInfo->name) }}
                    </span>
                @else
                    <span
                        class="text-xl md:text-2xl font-black tracking-tight text-slate-800 leading-tight uppercase font-mono">
                        History Transaksi Semua Rak
                    </span>
                @endif
            </h2>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-7 px-3 mb-4">
        <div
            class="cursor-pointer bg-white p-4 rounded-3xl border border-slate-100 shadow-sm group hover:bg-emerald-600/80 hover:border-emerald-600 hover:shadow-xl transition-all duration-300 active:scale-95">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-colors duration-300 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 17l-4 4m0 0l-4-4m4 4V3" />
                    </svg>
                </div>
                <div>
                    <p
                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-emerald-50">
                        In Today</p>
                    <p class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white">
                        {{ $totalMasuk }}
                        <span class="text-[10px] group-hover:text-emerald-100">PCS</span>
                    </p>
                </div>
            </div>
        </div>

        <div
            class="cursor-pointer bg-white p-4 rounded-3xl border border-slate-100 shadow-sm group hover:bg-rose-600/80 hover:border-rose-600 hover:shadow-xl transition-all duration-300 active:scale-95">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-colors duration-300 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7l4-4m0 0l4 4m-4-4v18" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-rose-50">
                        Out Today</p>
                    <p class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white">
                        {{ $totalKeluar }}
                        <span class="text-[10px] group-hover:text-rose-100">PCS</span>
                    </p>
                </div>
            </div>
        </div>

    </div>

    <div class="flex flex-wrap gap-2 mb-4">
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari material..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 min-w-[150px] focus:outline-none focus:ring-2 focus:ring-blue-300" />

        <select wire:model.live="filterTipe"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            <option value="">Semua Tipe</option>
            <option value="I">Masuk</option>
            <option value="O">Keluar</option>
        </select>

        <input type="date" wire:model.live="filterDate"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300" />
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Waktu</th>
                    @if (!$rakInfo)
                        <th class="px-4 py-3">Rak</th>
                    @endif
                    <th class="px-4 py-3">Material</th>
                    <th class="px-4 py-3 text-center">Tipe</th>
                    <th class="px-4 py-3 text-right">Qty</th>
                    <th class="px-4 py-3">User</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($history as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                            {{ $item->created_at->format('H:i') }}
                            <span class="block text-xs text-gray-400">{{ $item->created_at->format('d/m/Y') }}</span>
                        </td>
                        @if (!$rakInfo)
                            <td class="px-4 py-3 font-medium text-gray-700">
                                {{ strtoupper($item->rak->name ?? '-') }}
                            </td>
                        @endif
                        <td class="px-4 py-3 font-medium text-gray-700">
                            {{ $item->material->nama ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($item->stat === 'i')
                                <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">
                                    Masuk
                                </span>
                            @elseif($item->stat === 'o')
                                <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full">
                                    Keluar
                                </span>
                            @else
                                <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full">
                                    Tidak didukung
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">
                            {{ number_format($item->qty) }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $item->user->username ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            class="px-4 py-16 text-center text-xs text-slate-400 bg-slate-50 border-t font-bold uppercase tracking-widest">
                            Belum ada transaksi
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $history->links() }}
    </div>

</div>
