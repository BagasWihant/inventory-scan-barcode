@section('title', 'PDF Tenken Circuit')

<div class="min-h-screen text-slate-900 p-4 md:p-8 mx-auto max-w-6xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-lg md:text-xl font-black text-slate-800 uppercase italic tracking-tight">
                PDF terbaru — Tenken Circuit
            </h1>
            <p class="text-sm font-bold text-amber-700 mt-1">{{ date('d M Y', strtotime($tanggal)) }}</p>
        </div>
        <a href="{{ route('tenken.circuit', ['tanggal' => $tanggal]) }}"
            wire:navigate
            class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-black uppercase bg-slate-100 text-slate-800 hover:bg-slate-200 border border-slate-200">
            ← Kembali ke approval
        </a>
    </div>

    @if ($loadError)
        <div class="mb-4 rounded-xl border-l-4 border-rose-500 bg-rose-50 px-3 py-2 text-sm text-rose-800 font-medium">
            {{ $loadError }}
        </div>
    @endif

    @if ($pdfApprovalPath)
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex justify-between items-center px-4 py-3 border-b border-slate-100">
                <span class="text-xs font-black text-slate-600 uppercase">Ringkasan</span>
                <a href="{{ $pdfApprovalPath }}" target="_blank"
                    class="text-[10px] font-black bg-blue-600 text-white px-3 py-1.5 rounded-full uppercase tracking-widest hover:bg-blue-700">
                    Buka di tab baru
                </a>
            </div>
            <div class="w-full bg-slate-50 min-h-[70vh] md:min-h-[80vh]">
                <iframe src="{{ $pdfApprovalPath }}" class="w-full min-h-[70vh] md:min-h-[80vh] border-none"></iframe>
            </div>
        </div>
    @else
        <div class="rounded-2xl border-2 border-dashed border-slate-200 p-12 text-center text-slate-400 font-medium text-sm">
            Belum ada PDF (tidak ada data untuk tanggal ini).
        </div>
    @endif
</div>
