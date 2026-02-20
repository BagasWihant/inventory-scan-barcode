<div class="min-h-screen text-slate-900 p-4 md:p-8 max-w-7xl mx-auto" x-data="rakDetail"
    @keydown.window.escape="openModal = null">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div class="flex items-center gap-3">
            <a wire:navigate href="{{ route('rak.dashboard') }}"
                class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-2xl shadow-sm hover:bg-slate-50 transition text-slate-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Detail Rak</p>
                <h1
                    class="text-xl md:text-2xl font-black tracking-tight text-slate-800 leading-tight uppercase font-mono">
                    {{ $rak->name }}
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-3 self-end md:self-center">


            <button @click="openModal = 'material'"
                class="bg-indigo-600 text-white px-5 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition active:scale-95 text-sm">
                + Material
            </button>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 lg:gap-7 gap-3 mb-10 sm:place-items-center">

        <div
            class="cursor-pointer bg-white p-4 w-full rounded-3xl border border-slate-100 shadow-sm group hover:bg-indigo-600/80 hover:border-indigo-600 hover:shadow-xl transition-all duration-300 active:scale-95">
            <div class="flex items-center gap-4">
                <div
                    class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-colors duration-300 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <p
                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-indigo-50">
                        Total Material</p>
                    <p class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white"
                        x-text="listMaterial.length">
                    </p>
                </div>
            </div>
        </div>

        <div
            class="cursor-pointer bg-white p-4 w-full rounded-3xl border border-slate-100 shadow-sm group hover:bg-emerald-600/80 hover:border-emerald-600 hover:shadow-xl transition-all duration-300 active:scale-95">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-colors duration-300 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <div>
                    <p
                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-emerald-50">
                        Total Stok</p>
                    <p class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white"
                        x-text="listMaterial.reduce((a, m) => a + (+m.stok), 0)">
                    </p>
                </div>
            </div>
        </div>

        <div
            class="cursor-pointer bg-white p-4 w-full rounded-3xl border border-slate-100 shadow-sm group hover:bg-amber-500/80 hover:border-amber-500 hover:shadow-xl transition-all duration-300 active:scale-95">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center group-hover:bg-white/20 group-hover:text-white transition-colors duration-300 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest group-hover:text-amber-50">
                        Stok Rendah</p>
                    <p class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white"
                        x-text="listMaterial.filter(m => m.stok <= 5).length">
                    </p>
                </div>
            </div>
        </div>

        <a wire:navigate href="{{ route('rak.detail.history', ['rak_id' => $rak->id]) }}"
            class="cursor-pointer bg-white p-4 w-full rounded-3xl border border-slate-100 shadow-sm group hover:bg-emerald-500/80 hover:border-emerald-500 hover:shadow-xl transition-all duration-300 active:scale-95">
            <div class="flex items-center gap-4">
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
                    <p class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight group-hover:text-white">
                        <span x-text="historyCount"></span>
                        <span class="text-[10px] group-hover:text-emerald-100 uppercase">Transaksi</span>
                    </p>
                </div>
            </div>
        </a>

    </div>


    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="hidden md:table-header-group bg-slate-50/50 border-b border-slate-100">
                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="px-8 py-4">Kode</th>
                    <th class="px-4 py-4">Nama Material</th>
                    <th class="px-4 py-4">Stok</th>
                    <th class="px-4 py-4 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-50 font-medium text-slate-700">
                <template x-if="listMaterial.length === 0">
                    <tr>
                        <td colspan="4"
                            class="px-4 py-16 text-center text-xs text-slate-300 font-bold uppercase tracking-widest">
                            Belum ada material
                        </td>
                    </tr>
                </template>

                <template x-for="(item, index) in listMaterial" :key="item.id">
                    <tr class="block md:table-row hover:bg-indigo-50 transition-colors group p-4 md:p-0">

                        <td
                            class="block md:table-cell px-4 md:px-8 py-1 md:py-4 font-mono text-[10px] md:text-xs font-bold text-slate-400">
                            <span class="md:hidden text-slate-300 mr-2 uppercase">KODE:</span>
                            <span x-text="item.kode"></span>
                        </td>

                        <td class="block md:table-cell px-4 py-1 md:py-4 font-bold text-slate-800 text-base"
                            x-text="item.nama"></td>

                        <td class="block md:table-cell px-4 py-1 md:py-4">
                            <div class="flex items-center">
                                <span class="md:hidden text-[10px] text-slate-300 font-bold mr-2 uppercase">STOK:</span>
                                <span class="text-sm font-black"
                                    :class="item.stok <= 5 ? 'text-amber-500' : 'text-slate-800'"
                                    x-text="item.stok"></span>
                                <span class="text-[10px] text-slate-400 font-bold ml-1 uppercase"
                                    x-text="item.satuan"></span>

                                <span x-show="item.stok <= 5"
                                    class="ml-2 bg-amber-100 text-amber-600 text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-lg">
                                    Rendah
                                </span>
                            </div>
                        </td>

                        <td class="block md:table-cell px-4 py-4 md:py-4 text-center">
                            <div class="flex items-center justify-start md:justify-center gap-2">
                                <button @click="openTransModal(item, index)"
                                    class="flex-1 md:flex-none px-4 py-2.5 md:py-2 bg-indigo-600 md:bg-slate-100 md:text-slate-700 md:group-hover:bg-indigo-600 md:group-hover:text-white text-white rounded-xl text-[10px] font-black transition-all">
                                    + TRANSAKSI
                                </button>
                                <button @click="hapusMaterial(item.id, index)"
                                    class="px-4 py-2.5 md:py-2 bg-slate-100 hover:bg-rose-500 hover:text-white text-slate-400 rounded-xl text-[10px] font-black transition-all">
                                    <span class="md:hidden">HAPUS</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden md:block"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
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
            class="bg-white w-full max-w-md rounded-3xl shadow-2xl relative overflow-hidden p-8"
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <template x-if="openModal === 'material'">
                <div>

                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Tambah ke
                            </p>
                            <h2 class="text-2xl font-black uppercase italic text-indigo-600">{{ $rak->name }}</h2>
                        </div>
                        <button @click="openModal = null"
                            class="text-slate-400 hover:rotate-90 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-2 px-4 py-3 bg-indigo-50 rounded-2xl mb-4">
                        <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span class="text-xs font-black text-indigo-600 uppercase font-mono">{{ $rak->name }}</span>
                        <span class="ml-auto text-[9px] font-black text-indigo-300 uppercase tracking-widest">Rak
                            Terpilih</span>
                    </div>

                    <div class="space-y-3">

                        <input type="text" x-model="form.nama"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Nama Material *">

                        <input type="text" x-model="form.kode"
                            class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Kode (opsional)">

                        <div class="flex gap-3">
                            <div class="relative flex-1">
                                <input type="number" x-model="form.stok" placeholder="0" min="0"
                                    class="[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-black text-2xl text-indigo-600 outline-none focus:ring-2 focus:ring-indigo-500">
                                <span
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">QTY</span>
                            </div>
                            <div class="relative">
                                <div class="flex gap-1.5">
                                    <template x-for="s in ['PCS']" :key="s">
                                        <button type="button" @click="form.satuan = s"
                                            class="px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-200 active:scale-95 bg-slate-100 text-slate-500 hover:bg-slate-200"
                                            :class="form.satuan === s ?
                                                '!bg-indigo-600 !text-white shadow-md shadow-indigo-200' : ''"
                                            x-text="s">
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" @click="simpanMaterial()" :disabled="loadingForm"
                        class="w-full mt-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black shadow-lg shadow-indigo-100 transition active:scale-95 uppercase tracking-widest disabled:opacity-60 disabled:cursor-not-allowed"
                        x-text="loadingForm ? 'Menyimpan...' : 'Simpan Material'">
                    </button>

                </div>
            </template>

            <template x-if="openModal === 'trans'">
                <div>

                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Transaksi
                                Material</p>
                            <h2 class="text-xl font-black text-slate-800" x-text="selectedItem?.nama"></h2>
                        </div>
                        <button @click="openModal = null"
                            class="text-slate-400 hover:rotate-90 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-4 mb-5 text-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Stok Saat Ini
                        </p>
                        <p class="text-4xl font-black text-indigo-600" x-text="selectedItem?.stok"></p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase" x-text="selectedItem?.satuan"></p>
                    </div>

                    <div class="flex gap-3 mb-4">
                        <button @click="stokForm.tipe = 'in'"
                            :class="stokForm.tipe === 'in' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-100' :
                                'bg-slate-100 text-slate-500'"
                            class="flex-1 py-3 rounded-2xl font-black text-sm transition-all active:scale-95">
                            ↓ Masuk
                        </button>
                        <button @click="stokForm.tipe = 'out'"
                            :class="stokForm.tipe === 'out' ? 'bg-rose-500 text-white shadow-lg shadow-rose-100' :
                                'bg-slate-100 text-slate-500'"
                            class="flex-1 py-3 rounded-2xl font-black text-sm transition-all active:scale-95">
                            ↑ Keluar
                        </button>
                    </div>

                    <div class="relative mb-5">
                        <input type="number" x-model="stokForm.qty" placeholder="0" min="1"
                            class="[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none w-full px-5 py-4 bg-slate-50 border-none rounded-2xl font-black text-2xl outline-none focus:ring-2"
                            :class="stokForm.tipe === 'in' ? 'text-emerald-600 focus:ring-emerald-400' :
                                'text-rose-500 focus:ring-rose-400'">
                        <span
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase">QTY</span>
                    </div>

                    <button type="button" @click="simpanTransaksi()" :disabled="loadingTrans"
                        class="w-full py-4 text-white rounded-2xl font-black shadow-lg transition active:scale-95 uppercase tracking-widest disabled:opacity-60"
                        :class="stokForm.tipe === 'in' ? 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-100' :
                            'bg-rose-500 hover:bg-rose-600 shadow-rose-100'"
                        x-text="loadingTrans ? 'Menyimpan...' : (stokForm.tipe === 'in' ? 'Tambah Stok' : 'Kurangi Stok')">
                    </button>
                </div>
            </template>

            <p class="text-center mt-4 text-[9px] font-bold text-slate-300 uppercase tracking-widest">
                Tekan ESC atau klik luar untuk batal
            </p>
        </div>
    </div>

</div>

@script
    <script>
        Alpine.data('rakDetail', () => ({
            viewMode: 'grid',
            openModal: null,
            loadingForm: false,
            loadingTrans: false,
            rak_now: @json($rak ?? []),
            historyCount: @json($historyCount),
            listMaterial: @json($materials ?? []),

            form: {
                nama: '',
                kode: '',
                stok: 0,
                satuan: 'PCS'
            },

            selectedItem: null,
            selectedIndex: null,
            stokForm: {
                tipe: 'in',
                qty: ''
            },

            openTransModal(item, index) {
                this.selectedItem = {
                    ...item
                };
                this.selectedIndex = index;
                this.stokForm = {
                    tipe: 'in',
                    qty: ''
                };
                this.openModal = 'trans';
            },

            async simpanMaterial() {
                if (!this.form.nama.trim()) {
                    showToast('Nama material tidak boleh kosong', 'error');
                    return;
                }
                this.loadingForm = true;
                try {
                    await $wire.set('nama', this.form.nama.trim());
                    await $wire.set('kode', this.form.kode.trim());
                    await $wire.set('stok', parseInt(this.form.stok) || 0);
                    await $wire.set('satuan', this.form.satuan);

                    const newItem = await $wire.storeMaterial();

                    if (newItem) {
                        this.listMaterial.push(newItem);
                        this.form = {
                            nama: '',
                            kode: '',
                            stok: 0,
                            satuan: 'PCS'
                        };
                        this.openModal = null;
                        showToast('Material berhasil ditambahkan');
                    }
                } catch (e) {
                    showToast('Gagal menyimpan material', 'error');
                } finally {
                    this.loadingForm = false;
                }
            },

            async simpanTransaksi() {
                const qty = parseInt(this.stokForm.qty);
                if (!qty || qty < 1) {
                    showToast('Jumlah harus lebih dari 0', 'error');
                    return;
                }
                this.loadingTrans = true;
                try {
                    const result = await $wire.addTransaksi(this.rak_now, this.selectedItem, this.stokForm);
                    if (result?.error) {
                        showToast(result.error, 'error');
                        return;
                    }

                    this.openModal = null;
                    showToast(this.stokForm.tipe === 'in' ? `Transaksi masuk berhasil  ditambahkan` :
                        `Transaksi keluar berhasil ditambahkan`);
                } catch (e) {
                    showToast('Gagal update stok', 'error');
                } finally {
                    this.loadingTrans = false;
                }
            },

            async hapusMaterial(id, index) {
                const ok = await showConfirm({
                    title: 'Hapus Material?',
                    text: 'Material ini akan dihapus permanen.',
                    confirmText: 'Ya, Hapus',
                });
                if (!ok) return;

                try {
                    const success = await $wire.hapusMaterial(id);
                    if (success) {
                        this.listMaterial.splice(index, 1);
                        showToast('Material berhasil dihapus');
                    }
                } catch (e) {
                    showToast('Gagal menghapus material', 'error');
                }
            }
        }));
    </script>
@endscript
