<?php

namespace App\Livewire\Tenken;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Mpdf\Mpdf;

class Circuit extends Component
{
    public string $mesin = 'Circuit';
    public string $detailLine = '';

    public string $tanggal;


    /**
     * Baris siap tampil (kunci konsisten untuk blade).
     */
    public array $approvalRows = [];

    public ?string $loadError = null;

    public ?string $pdfPath = null;  // Simpan nama file temporary

    public function mount($tanggal): void
    {
        $this->tanggal = $tanggal;
        $this->loadApprovalData();
    }

    public function loadApprovalData(): void
    {
        $this->loadError = null;

        try {
            $raw = DB::connection('tenken')->select(
                'SET NOCOUNT ON; EXEC [dbo].[sp_approval_CT_PE] ?, ?',
                [
                    $this->mesin !== '' ? $this->mesin : null,
                    $this->tanggal !== '' ? $this->tanggal : null,
                ]
            );

            $this->approvalRows = array_map(
                fn($row) => $this->normalizeApprovalRow((array) $row),
                $raw
            );

            // Langsung generate file temp PDF setelah data siap
            if (\count($this->approvalRows) > 0) {
                $this->approvalPdfList();
            }
        } catch (\Throwable $e) {
            $this->loadError    = $e->getMessage();
            $this->approvalRows = [];
        }
    }

    private function approvalPdfList(): void
    {
        $mpdf = new Mpdf(['format' => 'A4']);
        $html = view('templates.pdf.tenken_circuit', [
            'data'    => $this->approvalRows,
            'mesin'   => $this->mesin,
            'tanggal' => $this->tanggal
        ])->render();

        $mpdf->WriteHTML($html);

        $fileName = $this->generateFN();
        $folder   = 'public/tenken/pdf';

        if (!Storage::exists($folder)) {
            Storage::makeDirectory($folder);
        }

        $path = $folder . '/' . $fileName;
        Storage::put($path, $mpdf->Output('', 'S'));

        $this->pdfPath = Storage::url($path) . '?v=' . time();
    }

    private function normalizeApprovalRow(array $row): array
    {
        $lower = [];
        foreach ($row as $k => $v) {
            $key         = strtolower(preg_replace('/\s+/', '_', trim((string) $k)));
            $lower[$key] = $v;
        }

        $first = function (array $candidates) use ($lower) {
            foreach ($candidates as $c) {
                $c = strtolower(str_replace(' ', '_', $c));
                if (array_key_exists($c, $lower) && $lower[$c] !== null && $lower[$c] !== '') {
                    return $lower[$c];
                }
            }
            foreach ($candidates as $c) {
                $c = strtolower(str_replace(' ', '_', $c));
                if (array_key_exists($c, $lower)) {
                    return $lower[$c];
                }
            }

            return null;
        };

        $namaLine     = $first(['nama_line', 'namaline', 'line', 'nm_line', 'nama_line_produksi']);
        $namaMember   = $first(['nama_member', 'member', 'nik', 'nrp', 'no_member', 'npk']);
        $leaderStatus = $first([
            'leader_approve_pe',
            'leader_aprove_pe',
            'approve_pe',
            'status_approve',
            'status',
            'leader_status',
        ]);
        $leaderTgl    = $first([
            'tanggal_approve_leader_pe',
            'tanggal_aprove_leader_pe',
            'tgl_approve_leader_pe',
            'tgl_approve',
            'waktu_approve',
        ]);

        $id        = $first(['id', 'rec_id', 'no_doc', 'no']);
        $detailRef = $id !== null && $id !== ''
            ? (string) $id
            : rawurlencode((string) $namaMember) . '|' . rawurlencode((string) $namaLine);

        return [
            'nama_line'                 => $namaLine,
            'nama_member'               => $namaMember,
            'leader_approve_pe'         => $leaderStatus,
            'tanggal_approve_leader_pe' => $leaderTgl,
            'detail_ref'                => $detailRef,
        ];
    }


    public function previewDetailPdf($namaLine)
    {
        $this->detailLine = $namaLine;
        $data = DB::connection('tenken')
        ->table('moni_chk_circuit as m') // Gunakan 'as' untuk alias yang lebih jelas
        ->selectRaw("
            m.item_pengecekan,
            m.metode_pengecekan,
            m.kriteria,
            ISNULL(h.hasil,'') AS hasil,
            ISNULL(h.keterangan,'') AS keterangan
        ")
        ->leftJoin('moni_history as h', function ($join) use ($namaLine) {
            $join->on('m.item_pengecekan', '=', 'h.item_pengecekan')
                 // Gunakan where() untuk nilai/value, bukan on()
                 ->where('h.tanggal', '=', $this->tanggal)
                 ->where('h.line', '=', $namaLine)
                 ->where('h.jenis_mesin', '=', $this->mesin);
        })
        ->orderBy('m.id')
        ->get();
        $html = view('templates.pdf.tenken_circuit_details', [
            'data'      => $data,
            'mesin'     => $this->mesin,
            'tanggal'   => $this->tanggal,
            'nama_line' => $namaLine
        ])->render();

        $mpdf = new Mpdf(['format' => 'A4']);
        $mpdf->WriteHTML($html);

        $fileName = $this->generateFN();
        $folder   = 'public/tenken/pdf/detail';

        if (!Storage::exists($folder)) {
            Storage::makeDirectory($folder);
        }

        $path = $folder . '/' . $fileName;
        Storage::put($path, $mpdf->Output('', 'S'));

        $this->pdfPath = Storage::url($path) . '?v=' . time();
    }

    private function generateFN(){
        if($this->detailLine){
            $fn = 'tenken_circuit_details_' . $this->mesin . '_' . $this->tanggal . '_' . $this->detailLine . '.pdf';
        }
        $fn = 'tenken_circuit_' . $this->mesin . '_' . $this->tanggal . '.pdf';
        return $fn;
    }

    public function generalPdf(){
        $this->detailLine = '';
        $this->pdfPath = Storage::url('public/tenken/pdf/' . $this->generateFN()) . '?v=' . time();
    }

    public function render()
    {
        return view('livewire.tenken.circuit');
    }
}
