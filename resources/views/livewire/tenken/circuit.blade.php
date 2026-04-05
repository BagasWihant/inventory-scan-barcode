@section('title', 'Approval Tenken Circuit')

<div class="min-h-screen text-slate-900 p-2 md:p-8 mx-auto max-w-7xl" x-data="{ openModal: false }"
    x-on:open-pdf-modal.window="openModal = true">

    <div class="flex flex-col gap-3 mb-6 mt-2">
        <h1
            class="text-base md:text-2xl font-black tracking-tight text-slate-800 text-center uppercase italic leading-tight">
            Approval TENKEN Circuit
        </h1>
        <div class="flex flex-col items-center gap-1.5">
            <div
                class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 border border-slate-200 rounded-full shadow-sm">
                <span class="text-[10px] md:text-sm font-black text-amber-600 tracking-wider">
                    {{ date('d M Y', strtotime($tanggal)) }}
                </span>
            </div>

        </div>
    </div>

    @if ($loadError)
        <div
            class="mb-4 rounded-xl border-l-4 border-rose-500 bg-rose-50 px-3 py-2 text-[10px] md:text-xs text-rose-800 font-medium">
            {{ $loadError }}
        </div>
    @endif

    <div class="rounded-2xl md:border md:border-slate-200 md:bg-white md:shadow-sm overflow-hidden">
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px] font-black">
                        <th class="px-4 py-4 text-left">Nama Line</th>
                        <th class="px-4 py-4 text-left">Member</th>
                        <th class="px-1 py-4 text-left">Leader Approve PE</th>
                        <th class="px-4 py-4 text-left">Tgl Approve</th>
                        <th class="px-4 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($approvalRows as $row)
                        <tr class="hover:bg-amber-50/30 transition-colors">
                            <td class="px-3 py-3 font-bold text-slate-800">{{ $row['nama_line'] ?? '—' }}</td>
                            <td class="px-3 py-1 text-slate-600">{{ $row['nama_member'] ?? '—' }}</td>
                            <td class="px-3 py-1">
                                @php $st = strtoupper(trim((string) ($row['leader_approve_pe'] ?? ''))); @endphp
                                @if ($st === 'APPROVED')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-xl text-[10px] font-black bg-green-100 text-green-700 ring-1 ring-green-600/20">APPROVED</span>
                                @elseif ($st === 'REJECTED')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-xl text-[10px] font-black bg-red-100 text-red-700 ring-1 ring-red-600/20">REJECTED</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-xl text-[10px] font-black bg-slate-100 text-slate-400 ring-1 ring-slate-600/10 italic">PENDING</span>
                                @endif
                            </td>
                            <td class="px-3 py-1 text-slate-600 text-[11px] font-mono">
                                {{ $row['tanggal_approve_leader_pe'] ? date('d-M-Y H:i', strtotime($row['tanggal_approve_leader_pe'])) : '—' }}</td>
                            <td class="px-3 py-1 text-center">
                                <button wire:click="previewDetailPdf('{{ $row['nama_line'] }}')"
                                    class="bg-amber-100 text-amber-700 px-2 py-1 rounded-xl text-[11px] font-black uppercase tracking-tighter hover:bg-amber-200 transition active:scale-95">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="py-20 text-center text-slate-400 font-black italic text-xs uppercase">Data Kosong
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 gap-3 md:hidden">
            @forelse ($approvalRows as $row)
                <div class="bg-white border border-slate-200 p-4 rounded-2xl shadow-sm active:bg-slate-50 transition"
                    wire:click="previewDetailPdf('{{ $row['nama_line'] }}')">
                    <div class="flex justify-between items-start mb-1">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Nama
                                Line</span>
                            <span class="text-sm font-black text-slate-800 italic">{{ $row['nama_line'] ?? '—' }}</span>
                        </div>
                        @php $st = strtoupper(trim((string) ($row['leader_approve_pe'] ?? ''))); @endphp
                        @if ($st === 'APPROVED')
                            <span
                                class="px-2 py-0.5 rounded text-[9px] font-black bg-green-100 text-green-700 ring-1 ring-green-600/20">APPROVED</span>
                        @elseif ($st === 'REJECTED')
                            <span
                                class="px-2 py-0.5 rounded text-[9px] font-black bg-red-100 text-red-700 ring-1 ring-red-600/20">REJECTED</span>
                        @else
                            <span
                                class="px-2 py-0.5 rounded text-[9px] font-black bg-slate-100 text-slate-400 ring-1 ring-slate-400/20">PENDING</span>
                        @endif
                    </div>

                    <div class="flex justify-between items-end border-t border-slate-50">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Member /
                                Date</span>
                            <span
                                class="text-[11px] text-slate-600 font-bold leading-none">{{ $row['nama_member'] ?? '—' }}</span>
                            <span
                                class="text-[9px] text-slate-400 font-mono mt-1">{{ $row['tanggal_approve_leader_pe'] ?? '—' }}</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-[9px] font-black text-amber-600 uppercase mb-1">Ketuk untuk Detail</span>
                            <div
                                class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 shadow-inner">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-10 text-center rounded-2xl border-2 border-dashed border-slate-200">
                    <p class="text-[10px] font-black text-slate-400 uppercase italic">Tidak Ada Data</p>
                </div>
            @endforelse
        </div>
    </div>

    @if ($pdfApprovalPath)
        <div class="mt-8 bg-white p-3 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xs font-black text-slate-800 uppercase italic">General PDF</h2>
                <a href="{{ $pdfApprovalPath }}" target="_blank"
                    class="text-[9px] font-black bg-blue-600 text-white px-4 py-2 rounded-full uppercase tracking-widest shadow-md active:scale-95">Open in new tab</a>
            </div>
            <div class="w-full border border-slate-100 rounded-xl overflow-hidden bg-slate-50 h-[350px] md:h-[700px]">
                <iframe src="{{ $pdfApprovalPath }}" class="w-full h-full border-none"></iframe>
            </div>
        </div>
    @endif

    <div x-show="openModal" class="fixed inset-0 z-[99] flex items-center justify-center p-2 sm:p-4"
        style="display: none;">
        <div class="absolute inset-0 bg-slate-900/90 backdrop-blur-sm" @click="openModal = false" x-transition.opacity>
        </div>

        <div class="bg-white w-full max-w-5xl rounded-[1.5rem] shadow-2xl overflow-hidden flex flex-col h-[95vh] relative z-10"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-full"
            x-transition:enter-end="opacity-100 translate-y-0">

            <div class="flex items-center justify-between p-4 border-b bg-white">
                <div class="flex flex-col">
                    <span class="text-[8px] font-black text-amber-500 uppercase tracking-widest">Detail Pdf</span>
                    <h3 class="text-[11px] md:text-sm font-black text-slate-800 uppercase italic leading-none">LINE:
                        {{ $detailLine }}</h3>
                </div>
                <button @click="openModal = false" class="p-2 bg-slate-100 rounded-lg text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="flex-1 bg-slate-100">
                @if ($pdfPath)
                    <iframe src="{{ $pdfPath }}" class="w-full h-full border-none shadow-inner bg-white"></iframe>
                @else
                    <div class="flex flex-col items-center justify-center h-full gap-3">
                        <div class="w-8 h-8 border-4 border-amber-500 border-t-transparent rounded-full animate-spin">
                        </div>
                        <span
                            class="text-[9px] font-black text-slate-400 uppercase tracking-widest animate-pulse">Menyusun
                            Data...</span>
                    </div>
                @endif
            </div>

            <a href="{{ $pdfPath }}" target="_blank"
                class="block w-full py-3 bg-blue-600 text-white text-center text-[10px] font-black uppercase tracking-widest lg:hidden">
                Open in new tab
            </a>
        </div>
    </div>
</div>
