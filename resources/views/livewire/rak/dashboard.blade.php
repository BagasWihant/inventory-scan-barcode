<div class="min-h-screen text-slate-900 p-4 md:p-8" x-data="{ viewMode: 'grid', openModal: null }"
    @keydown.window.escape="openModal = null">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div class="flex items-center gap-4">
            <h1 class="text-xl md:text-2xl font-black tracking-tight text-slate-800 leading-tight">Dashboard Rak</h1>
        </div>

        <div class="flex items-center gap-3 self-end md:self-center">
            <div class="flex bg-white border border-slate-200 p-1.5 rounded-2xl shadow-sm">
                <button @click="viewMode = 'grid'"
                    :class="viewMode === 'grid' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400'"
                    class="p-2 rounded-xl transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 14a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 14a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
                <button @click="viewMode = 'list'"
                    :class="viewMode === 'list' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400'"
                    class="p-2 rounded-xl transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <div class="flex gap-2">
                <button @click="openModal = 'rak'"
                    class="bg-white text-teal-600 border border-emerald-100 px-5 py-3 rounded-2xl font-bold shadow-sm hover:bg-teal-50 transition active:scale-95 text-sm">+
                    Rak</button>
                <button @click="openModal = 'material'"
                    class="bg-indigo-600 text-white px-5 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition active:scale-95 text-sm">+
                    Material</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-10">


        <div
            class="cursor-pointer bg-white p-4 rounded-3xl border border-slate-100 shadow-sm group hover:bg-teal-600/80 hover:border-teal-600 hover:shadow-xl transition-all duration-300 active:scale-95">
            <div class="flex items-center lg:flex-col lg:items-start gap-4">
                <div
                    class="w-12 h-12 bg-slate-50 text-slate-600 rounded-2xl flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-colors duration-300 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div>
                    <p
                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-slate-300">
                        Total Rak</p>
                    <p class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white">42
                        <span class="text-[10px] group-hover:text-slate-300">TITIK</span>
                    </p>
                </div>
            </div>
        </div>

        <div
            class="cursor-pointer bg-white p-4 rounded-3xl border border-slate-100 shadow-sm group hover:bg-indigo-600/80 hover:border-indigo-600 hover:shadow-xl transition-all duration-300 active:scale-95">
            <div class="flex items-center lg:flex-col lg:items-start gap-4">
                <div
                    class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-colors duration-300 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <p
                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-indigo-50">
                        Material</p>
                    <p class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white">1,240
                        <span class="text-[10px] group-hover:text-indigo-100">SKU</span>
                    </p>
                </div>
            </div>
        </div>

        <div
            class="cursor-pointer bg-white p-4 rounded-3xl border border-slate-100 shadow-sm group hover:bg-emerald-600/80 hover:border-emerald-600 hover:shadow-xl transition-all duration-300 active:scale-95">
            <div class="flex items-center lg:flex-col lg:items-start gap-4">
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
                    <p class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white">124
                        <span class="text-[10px] group-hover:text-emerald-100">PCS</span>
                    </p>
                </div>
            </div>
        </div>

        <div
            class="cursor-pointer bg-white p-4 rounded-3xl border border-slate-100 shadow-sm group hover:bg-rose-600/80 hover:border-rose-600 hover:shadow-xl transition-all duration-300 active:scale-95">
            <div class="flex items-center lg:flex-col lg:items-start gap-4">
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
                    <p class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white">86
                        <span class="text-[10px] group-hover:text-rose-100">PCS</span>
                    </p>
                </div>
            </div>
        </div>

    </div>

    <div x-show="viewMode === 'grid'" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach (range(1, 4) as $i)
            <div
                class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm group hover:shadow-xl transition-all duration-300 relative overflow-hidden">
                <div class="flex justify-between items-start mb-6">
                    <div
                        class="flex items-center gap-1.5 px-3 py-1 bg-slate-100 rounded-full text-[9px] font-black text-slate-500 uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> ACTIVE
                    </div>
                </div>
                <h3 class="font-bold text-indigo-600 text-lg bg-indigo-50 px-2.5 py-2 rounded uppercase font-mono leading-tight mb-4">RAK 
                    {{ chr(64 + $i) }}</h3>
                <div
                    class="space-y-3 relative z-10 border-t border-slate-50 pt-4 text-[10px] font-bold text-slate-400 uppercase">
                    <div class="flex justify-between items-center"><span>Jumlah Item</span><span
                            class="text-xs text-slate-700 uppercase">512 Pcs</span></div>
                </div>
                <button
                    class="w-full mt-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-2xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-indigo-100">DETAIL
                    RAK</button>
            </div>
        @endforeach
    </div>

    <div x-show="viewMode === 'list'" x-transition x-cloak
        class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50/50 border-b border-slate-100">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="px-8 py-5">Kode</th>
                    <th class="px-8 py-5">Stok</th>
                    <th class="px-8 py-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 font-medium text-slate-700">
                @foreach (range(1, 4) as $i)
                    <tr class="hover:bg-indigo-50/30 transition-colors group italic">
                        <td class="px-8 py-5 font-mono text-xs font-bold text-indigo-600">A{{ $i }}</td>
                        <td class="px-8 py-5 text-sm font-black text-slate-800 uppercase">512 PCS</td>
                        <td class="px-8 py-5 text-center"><button
                                class="px-4 py-2 bg-slate-100 group-hover:bg-indigo-600 group-hover:text-white rounded-xl text-[10px] font-black transition-all">DETAIL</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="openModal" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        x-transition.opacity>
        <div @click.away="openModal = null" class="bg-white w-full max-w-md rounded-3xl shadow-2xl relative p-8"
            x-show="openModal" x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-2xl font-black uppercase italic"
                    :class="openModal === 'rak' ? 'text-teal-600' : 'text-indigo-600'"
                    x-text="openModal === 'rak' ? 'Tambah Rak' : 'Input Material'"></h2>
                <button @click="openModal = null"
                    class="text-slate-400 hover:rotate-90 transition-all duration-300"><svg class="w-6 h-6"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>

            <form class="space-y-4">
                <div x-show="openModal === 'material'">
                    <select
                        class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 appearance-none">
                        <option>Pilih Rak...</option>
                        <option>RAK-A1</option>
                    </select>
                </div>
                <input type="text"
                    class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 outline-none focus:ring-2"
                    :class="openModal === 'rak' ? 'focus:ring-teal-500' : 'focus:ring-indigo-500'"
                    :placeholder="openModal === 'rak' ? 'Kode Rak (RAK-01)' : 'Nama Barang'">
                <div x-show="openModal === 'material'" class="relative">
                    <input type="number" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                        class="[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-black text-2xl text-indigo-600 outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="0">
                    <span
                        class="absolute right-5 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">QTY</span>
                </div>
                <button type="submit"
                    class="w-full py-4 text-white rounded-2xl font-black shadow-lg transition active:scale-95 uppercase tracking-widest"
                    :class="openModal === 'rak' ? 'bg-teal-600 hover:bg-teal-700 shadow-teal-100' :
                        'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-100'"
                    x-text="openModal === 'rak' ? 'Simpan Rak' : 'Input Stok'"></button>
            </form>
            <p class="text-center mt-4 text-[9px] font-bold text-slate-300 uppercase tracking-widest">Tekan ESC atau
                klik luar untuk batal</p>
        </div>
    </div>
</div>
