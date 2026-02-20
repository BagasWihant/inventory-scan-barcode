<div class="min-h-screen text-slate-900 p-4 md:p-8 mx-auto max-w-7xl" x-data="mainJs"
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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-10">

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
                    <span class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white"
                        x-text="total.rak">
                    </span>
                    <span class="text-[10px] group-hover:text-slate-300 text-gray-500">RAK</span>
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
                    <span class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white"
                        x-text="total.all">
                    </span>
                    <span class="text-[10px] group-hover:text-indigo-100 text-gray-500">Material</span>
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
                    <span class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white"
                        x-text="total.in">
                    </span>
                    <span class="text-[10px] group-hover:text-emerald-100 text-gray-500">PCS</span>
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
                    <span class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white"
                        x-text="total.out">
                    </span>
                    <span class="text-[10px] group-hover:text-rose-100 text-gray-500">PCS</span>
                </div>
            </div>
        </div>

        <a wire:navigate href="{{ route('rak.history') }}"
            class="cursor-pointer bg-white p-4 rounded-3xl border border-slate-100 shadow-sm group hover:bg-emerald-500/80 hover:border-emerald-500 hover:shadow-xl transition-all duration-300 active:scale-95">
            <div class="flex items-center lg:flex-col lg:items-start gap-4">
                <div
                    class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-colors duration-300 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p
                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-emerald-50">
                        History Today</p>
                    <span class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white"
                        x-text="total.history">
                    </span>
                    <span class="text-[10px] group-hover:text-emerald-50 text-gray-500">Transaksi</span>
                </div>
            </div>
        </a>

    </div>

    <div x-show="viewMode === 'grid'" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <template x-for="(rak, index) in listRak" :key="rak.id">
            <div
                class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm group  hover:shadow-xl transition-all duration-300 relative overflow-hidden">
                <div class="flex justify-between items-start mb-6">
                    <div
                        class="flex items-center gap-1.5 px-3 py-1 bg-slate-100 rounded-full text-[9px] font-black text-slate-500 uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> ACTIVE
                    </div>
                </div>
                <h3
                    class="font-bold text-indigo-600 text-lg bg-indigo-50 px-2.5 py-2 rounded uppercase font-mono mb-4">
                    <span x-text="rak.nama_rak"></span>
                </h3>
                <div class="space-y-3 pt-4 text-[10px] font-bold text-slate-400 uppercase">
                    <div class="flex justify-between items-center">
                        <span>Jumlah Material</span>
                        <span class="text-xs text-slate-700 uppercase"
                            x-text="(rak.total_material ?? 0) + ' Material'"></span>
                    </div>
                </div>
                <div class="space-y-3 pt-4 text-[10px] font-bold text-slate-400 uppercase">
                    <div class="flex justify-between items-center">
                        <span>Total Qty</span>
                        <span class="text-xs text-slate-700 uppercase" x-text="(rak.total_stok ?? 0) + ' Pcs'"></span>
                    </div>
                </div>
                <a wire:navigate :href="'{{ route('rak.detail', ['rak_id' => '_ID_']) }}'.replace('_ID_', rak.id)"
                    class="w-full mt-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-2xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-indigo-100">
                    DETAIL RAK
                </a>
            </div>
        </template>
    </div>

    <div x-show="viewMode === 'list'" x-transition x-cloak
        class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50/50 border-b border-slate-100">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="px-8 py-5">Nama</th>
                    <th class="px-8 py-5">Jumlah Material</th>
                    <th class="px-8 py-5">Total Qty</th>
                    <th class="px-8 py-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 font-medium text-slate-700">
                <template x-for="(rak, index) in listRak" :key="rak.id">
                    <tr class="hover:bg-indigo-50/30 transition-colors group">
                        <td class="px-8 py-5 font-mono text-xs font-bold text-indigo-600" x-text="rak.nama_rak"></td>
                        <td class="px-8 py-5 text-sm font-black text-slate-800 uppercase"
                            x-text="(rak.total_material ?? 0) + ' Material'"></td>
                        <td class="px-8 py-5 text-sm font-black text-slate-800 uppercase"
                            x-text="(rak.total_stok ?? 0) + ' PCS'"></td>
                        <td class="px-8 py-5 text-center">
                            <button
                                class="px-4 py-2 bg-slate-100 group-hover:bg-indigo-600 group-hover:text-white rounded-xl text-[10px] font-black transition-all">
                                DETAIL
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>


    <div x-show="openModal" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition duration-200 ease-in"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div @click.away="openModal = null"
            class="bg-white w-full max-w-md rounded-3xl shadow-2xl relative overflow-hidden"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <template x-if="openModal === 'rak'">
                <div class="max-w-md mx-auto">
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-7">
                            <div>
                                <p class="text-[9px] font-black tracking-[0.2em] text-slate-400 uppercase mb-1">
                                    Warehouse</p>
                                <h2 class="text-2xl font-black text-teal-600">Tambah Rak Baru</h2>
                            </div>
                            <button @click="openModal = null"
                                class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-400 hover:rotate-90 transition-all duration-200 shrink-0 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama
                            Rak</label>
                        <input type="text" x-model="inputNamaRak" @keydown.enter="simpanRak()"
                            class="w-full px-5 py-4 bg-slate-50 rounded-2xl font-bold text-slate-800 text-sm outline-none border-2 border-transparent focus:border-teal-400 focus:bg-white transition-all duration-200 placeholder-slate-300"
                            placeholder="Contoh: RAK-A1, RAK-01 ...">

                        <button type="button" @click="simpanRak()" :disabled="loading"
                            class="w-full mt-6 py-4 bg-teal-500 hover:bg-teal-600 text-white rounded-2xl font-black shadow-lg shadow-teal-100 transition-all active:scale-95 uppercase tracking-widest text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            x-text="loading ? 'Menyimpan...' : 'Simpan Rak'">
                        </button>

                        <p class="text-center mt-4 text-[9px] font-bold text-slate-300 uppercase tracking-widest">
                            ESC atau klik luar untuk batal
                        </p>
                    </div>
                </div>
            </template>

            <template x-if="openModal === 'material'">
                <div class="max-w-md mx-auto">
                    <div class="p-8">
                        <div class="flex justify-between items-start mb-7">
                            <div>
                                <p class="text-[9px] font-black tracking-[0.2em] text-slate-400 uppercase mb-1">
                                    Inventory</p>
                                <h2 class="text-2xl font-black text-indigo-600">Input Material</h2>
                            </div>
                            <button @click="openModal = null"
                                class="w-9 h-9 flex items-center justify-center rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-400 hover:rotate-90 transition-all duration-200 shrink-0 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-5">


                            <div x-data="{ open: false, selected: null, search: '' }" x-init="$watch('formMaterial.rakId', v => { selected = listRak.find(r => r.id == v) ?? null })">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Pilih Rak <span class="text-rose-400">*</span>
                                </label>
                                <div class="relative">

                                    <button type="button"
                                        @click="open = !open; if(open) $nextTick(() => $refs.searchRak.focus())"
                                        class="w-full px-5 py-4 bg-slate-50 rounded-2xl font-bold text-sm outline-none border-2 transition-all duration-200 flex items-center justify-between gap-3"
                                        :class="open ? 'border-indigo-400 bg-white' :
                                            'border-transparent hover:border-slate-200'">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition-colors"
                                                :class="selected ? 'bg-indigo-100' : 'bg-slate-200'">
                                                <svg class="w-3 h-3"
                                                    :class="selected ? 'text-indigo-600' : 'text-slate-400'"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                                </svg>
                                            </div>
                                            <span :class="selected ? 'text-slate-800' : 'text-slate-300'"
                                                x-text="selected ? selected.nama_rak : 'Pilih rak tujuan...'"></span>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                        
                                            <span x-show="selected"
                                                @click.stop="formMaterial.rakId = ''; selected = null; search = ''"
                                                class="w-5 h-5 flex items-center justify-center rounded-full bg-slate-200 hover:bg-rose-100 hover:text-rose-500 text-slate-400 transition-colors cursor-pointer">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </span>
                                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                                :class="open ? 'rotate-180' : ''" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </button>

                                    <div x-show="open" @click.away="open = false; search = ''"
                                        x-transition:enter="transition duration-150 ease-out"
                                        x-transition:enter-start="opacity-0 -translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition duration-100"
                                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-10">

                                        <div class="p-2 border-b border-slate-100">
                                            <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-xl">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                                <input type="text" x-model="search" x-ref="searchRak"
                                                    class="w-full bg-transparent text-xs font-bold text-slate-700 outline-none placeholder-slate-300"
                                                    placeholder="Cari rak...">
                                                <button type="button" x-show="search" @click="search = ''"
                                                    class="text-slate-300 hover:text-slate-500 transition-colors shrink-0">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="max-h-48 overflow-y-auto">
                                            <template
                                                x-if="listRak.filter(r => r.nama_rak.toLowerCase().includes(search.toLowerCase())).length === 0">
                                                <div class="px-5 py-4 text-xs text-slate-400 font-bold text-center">
                                                    Rak "<span x-text="search"></span>" tidak ditemukan
                                                </div>
                                            </template>
                                            <template
                                                x-for="rak in listRak.filter(r => r.nama_rak.toLowerCase().includes(search.toLowerCase()))"
                                                :key="rak.id">
                                                <button type="button"
                                                    @click="formMaterial.rakId = rak.id; selected = rak; open = false; search = ''"
                                                    class="w-full px-5 py-3 flex items-center gap-3 hover:bg-indigo-50 transition-colors text-left group">
                                                    <div
                                                        class="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-indigo-100 flex items-center justify-center shrink-0 transition-colors">
                                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-600 transition-colors"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-sm font-bold text-slate-700 group-hover:text-indigo-700 uppercase font-mono"
                                                        x-text="rak.nama_rak"></span>
                                                    <svg x-show="formMaterial.rakId == rak.id"
                                                        class="w-4 h-4 text-indigo-500 ml-auto shrink-0"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-px bg-slate-100"></div>
                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Detail
                                    Barang</span>
                                <div class="flex-1 h-px bg-slate-100"></div>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Nama Barang <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="formMaterial.nama"
                                    class="w-full px-5 py-4 bg-slate-50 rounded-2xl font-bold text-slate-800 text-sm outline-none border-2 border-transparent focus:border-indigo-400 focus:bg-white transition-all duration-200 placeholder-slate-300"
                                    placeholder="Nama barang...">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Kode <span class="text-slate-300 font-medium normal-case tracking-normal">—
                                        opsional</span>
                                </label>
                                <input type="text" x-model="formMaterial.kode"
                                    class="w-full px-5 py-4 bg-slate-50 rounded-2xl font-bold text-slate-800 text-sm outline-none border-2 border-transparent focus:border-indigo-400 focus:bg-white transition-all duration-200 placeholder-slate-300 font-mono"
                                    placeholder="SKU-001, BRG-A1 ...">
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                    Jumlah & Satuan
                                </label>
                                <div class="flex gap-3">
                                    <div class="relative flex-1">
                                        <input type="number" x-model="formMaterial.qty" min="0"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                            class="[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none w-full px-5 py-4 bg-slate-50 rounded-2xl font-black text-2xl text-indigo-600 outline-none border-2 border-transparent focus:border-indigo-400 focus:bg-white transition-all duration-200"
                                            placeholder="0">
                                        <span
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-black text-slate-300 uppercase tracking-widest">QTY</span>
                                    </div>
                                    <div class="flex flex-col gap-1.5 justify-center">
                                        <div class="flex gap-1.5">
                                            <template x-for="s in ['PCS']" :key="s">
                                                <button type="button" @click="formMaterial.satuan = s"
                                                    class="px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-200 active:scale-95 bg-slate-100 text-slate-500 hover:bg-slate-200"
                                                    :class="formMaterial.satuan === s ?
                                                        '!bg-indigo-600 !text-white shadow-md shadow-indigo-200' : ''"
                                                    x-text="s">
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" @click="simpanMaterial()" :disabled="loading"
                            class="w-full mt-7 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black shadow-lg shadow-indigo-100 transition-all active:scale-95 uppercase tracking-widest text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            x-text="loading ? 'Menyimpan...' : 'Simpan Material'">
                        </button>

                        <p class="text-center mt-4 text-[9px] font-bold text-slate-300 uppercase tracking-widest">
                            ESC atau klik luar untuk batal
                        </p>
                    </div>
                </div>
            </template>

        </div>
    </div>

</div>

</div>

@script
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        function showToast(message, icon = 'success') {
            Toast.fire({
                icon,
                title: message
            });
        }

        Alpine.data('mainJs', () => ({
            viewMode: 'grid',
            openModal: null,
            loading: false,
            listRak: @json($listRak ?? []),
            total: @json($total ?? []),
            inputNamaRak: '',
            inputQty: '',
            formMaterial: {
                rakId: '',
                nama: '',
                kode: '',
                qty: 0,
                satuan: 'PCS',
            },

            // Reset form saat modal dibuka
            init() {
                this.$watch('openModal', val => {
                    if (!val) {
                        this.inputNamaRak = '';
                        this.formMaterial = {
                            rakId: '',
                            nama: '',
                            kode: '',
                            qty: 0,
                            satuan: 'PCS'
                        };
                        this.loading = false;
                    }
                });
            },

            async simpanRak() {
                if (!this.inputNamaRak.trim()) {
                    showToast('Nama rak tidak boleh kosong', 'error');
                    return;
                }
                this.loading = true;
                try {
                    const newRak = await $wire.storeRak(this.inputNamaRak.trim());
                    if (newRak) {
                        this.listRak.push({
                            id: newRak.id,
                            nama_rak: newRak.nama_rak,
                            kode_rak: newRak.kode_rak,
                            total_stok: 0,
                            total_material: 0,
                        });
                        this.totalRak++;
                        this.openModal = null;
                        this.inputNamaRak = '';
                        showToast('Rak berhasil ditambahkan');
                    }
                } catch (e) {
                    showToast('Gagal menyimpan rak', 'error');
                } finally {
                    this.loading = false;
                }
            },

            async hapusRak(id, index) {
                const result = await Swal.fire({
                    title: 'Hapus Rak?',
                    text: 'Data rak ini akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6366f1',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                });

                if (!result.isConfirmed) return;

                try {
                    const success = await $wire.destroyRak(id);
                    if (success) {
                        this.listRak.splice(index, 1);
                        this.totalRak--;
                        showToast('Rak berhasil dihapus');
                    }
                } catch (e) {
                    showToast('Gagal menghapus rak', 'error');
                }
            },

            async inputStok() {
                showToast('Fitur input stok belum diimplementasi', 'warning');
            },

            async simpanMaterial() {
                if (!this.formMaterial.rakId) {
                    showToast('Pilih rak terlebih dahulu', 'error');
                    return;
                }
                if (!this.formMaterial.nama.trim()) {
                    showToast('Nama barang tidak boleh kosong', 'error');
                    return;
                }

                this.loading = true;
                try {
                    const result = await $wire.storeMaterial(
                        this.formMaterial.rakId,
                        this.formMaterial.nama.trim(),
                        this.formMaterial.kode.trim(),
                        parseInt(this.formMaterial.qty) || 0,
                        this.formMaterial.satuan
                    );

                    if (result) {
                        // Update total_stok di card rak yang dipilih
                        const idx = this.listRak.findIndex(r => r.id == this.formMaterial.rakId);
                        if (idx !== -1) {
                            this.listRak[idx].total_stok = parseInt(this.listRak[idx].total_stok ?? 0) + (
                                parseInt(
                                    this.formMaterial.qty) || 0);
                            this.listRak[idx].total_material = parseInt(this.listRak[idx].total_material ??
                                0) + parseInt(1);
                        }
                        this.openModal = null;
                        showToast('Material berhasil ditambahkan');
                    }
                } catch (e) {
                    showToast('Gagal menyimpan material', 'error');
                } finally {
                    this.loading = false;
                }
            },

        }));
    </script>
@endscript
