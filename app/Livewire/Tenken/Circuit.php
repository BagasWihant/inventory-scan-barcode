<?php

namespace App\Livewire\Tenken;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Circuit extends Component
{
    public string $mesin = 'Circuit';
    public string $tanggal;
    public string $detailLine       = '';
    public array $approvalRows      = [];
    public ?string $loadError       = null;
    public ?string $pdfPath         = null;
    public ?string $pdfApprovalPath = null;
    public ?string $level           = null;
    public $dataIds                 = [];
    public string $rejectReason     = '';

    #[Locked]
    public bool $pdfOnly = false;

    private const STATUS_FOREMAN          = 'foreman';
    private const STATUS_SPV              = 'spv';
    private const STATUS_MANAGER          = 'manager';
    private const STATUS_APPROVED         = 'approved';
    private const STATUS_REJECTED_FOREMAN = 'rejected_foreman';
    private const STATUS_REJECTED_SPV     = 'rejected_spv';
    private const STATUS_REJECTED_MANAGER = 'rejected_manager';

    /**
     * array NIK=>Nama
     * @var array
     */
    private const FOREMAN_NIK = [
        ['nik' => '1234', 'nama' => 'Foreman 1'],
        ['nik' => '5678', 'nama' => 'Foreman 2'],
    ];

    /**
     * array NIK=>Nama
     * @var array
     */
    private const SPV_NIK = [
        ['nik' => '2222', 'nama' => 'SPV 1'],
        ['nik' => '3333', 'nama' => 'SPV 2'],
    ];

    private const MANAGER_NIK = [
        ['nik' => '9999', 'nama' => 'Manager'],
    ];

    public function mount($tanggal, bool $pdfOnly = false): void
    {
        $this->pdfOnly = $pdfOnly;
        $this->tanggal = date('Y-m-d', strtotime($tanggal));
        $this->loadApprovalData();
        if (count($this->approvalRows) > 0) {
            $firstdata = $this->approvalRows[0];

            // approve foreman jika belum
            if (empty($firstdata['nikforeman1']) || empty($firstdata['nikforeman2'])) {
                $this->approveByLevel(self::STATUS_FOREMAN);
            }
            $this->approvalPdfList(false);
        }
    }

    public function loadApprovalData(): void
    {
        $this->loadError = null;

        try {
            $raw                = DB::connection('tenken')->select(
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
            $this->dataIds      = collect($raw)->pluck('id');
            $this->level        = $this->approvalRows[0]['status'] ?? null;
        } catch (\Throwable $e) {
            $this->loadError    = $e->getMessage();
            $this->approvalRows = [];
            $this->dataIds      = [];
            $this->level        = null;
        }
    }

    /**
     * Label TTD per kolom: "tanggal — level". Isi mengikuti tahap approval saat ini.
     *
     * @return array{foreman: ?string, spv: ?string, manager: ?string}
     */
    public function buatTtd(): array
    {
        $row      = $this->approvalRows[0] ?? null;
        $foreman1 = $row['nikforeman1'] ?? self::FOREMAN_NIK[0]['nik'];
        $foreman2 = $row['nikforeman2'] ?? self::FOREMAN_NIK[1]['nik'];
        $spv1     = $row['nikspv1'] ?? self::SPV_NIK[0]['nik'];
        $spv2     = $row['nikspv2'] ?? self::SPV_NIK[1]['nik'];
        $manager  = $row['nikmanager'] ?? self::MANAGER_NIK[0]['nik'];

        $line = fn(mixed $string, string $level): ?string => !$this->textValueIsFilled($string)
            ? 'default-kosong'
            : $this->formatTtdLabel($string, $level);

        $ttdForeman = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($line($foreman1 . '_' . $foreman2, self::STATUS_FOREMAN)));
        $ttdSpv     = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($line($spv1 . '_' . $spv2, self::STATUS_SPV)));
        $ttdManager = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($line($manager, self::STATUS_MANAGER)));

        return match ($this->level) {
            self::STATUS_FOREMAN  => [
                'foreman' => null,
                'spv'     => null,
                'manager' => null,
            ],
            self::STATUS_SPV      => [
                'foreman' => $ttdForeman,
                'spv'     => null,
                'manager' => null,
            ],
            self::STATUS_MANAGER  => [
                'foreman' => $ttdForeman,
                'spv'     => $ttdSpv,
                'manager' => null,
            ],
            self::STATUS_APPROVED => [
                'foreman' => $ttdForeman,
                'spv'     => $ttdSpv,
                'manager' => $ttdManager,
            ],
            default               => [
                'foreman' => null,
                'spv'     => null,
                'manager' => null,
            ],
        };
    }

    private function formatTtdLabel(string $text, string $level): string
    {
        // if ($date instanceof \DateTimeInterface) {
        //     $tanggal = $date->format('d-M-Y');
        // } else {
        //     $ts = is_numeric($date)
        //         ? (int) $date
        //         : strtotime((string) $date);
        //     $tanggal = $ts ? date('d-M-Y', $ts) : trim((string) $date);
        // }
        $label     = match (strtolower($level)) {
            self::STATUS_FOREMAN => 'Foreman',
            self::STATUS_SPV     => 'SPV',
            self::STATUS_MANAGER => 'Manager',
            default              => ucfirst($level),
        };

        return $text . '_' . $label;
    }

    private function approvalPdfList($regenerate = false): void
    {
        $folder   = 'public/tenken/pdf';
        $fileName = $this->generateFN();
        $path     = $folder . '/' . $this->level . '_' . $fileName;
        if (Storage::exists($path) && !$regenerate) {
            $this->pdfApprovalPath = Storage::url($path) . '?v=' . time();

            return;
        }

        $mpdf = new Mpdf(['format' => 'A4']);
        $html = view('templates.pdf.tenken_circuit', [
            'data'    => $this->approvalRows,
            'mesin'   => $this->mesin,
            'tanggal' => $this->tanggal,
            'ttd'     => $this->buatTtd(),
        ])->render();

        $mpdf->WriteHTML($html);

        if (!Storage::exists($folder)) {
            Storage::makeDirectory($folder);
        }

        Storage::put($path, $mpdf->Output('', 'S'));

        $this->pdfApprovalPath = Storage::url($path) . '?v=' . time();
    }

    /**
     * ini buat ubah nama key nya, karna dari store prosedur itu
     * Nama Line => nama_line,
     * kalo key di array/objek gabisa spasi
     */
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

        $namaLine     = $first(['nama_line']);
        $namaMember   = $first(['nama_member']);
        $leaderStatus = $first([
            'status'
        ]);
        $leaderTgl    = $first([
            'tanggal_aprove_leader_pe'
        ]);

        $id        = $first(['id', 'rec_id', 'no_doc', 'no']);
        $detailRef = $id !== null && $id !== ''
            ? (string) $id
            : rawurlencode((string) $namaMember) . '|' . rawurlencode((string) $namaLine);

        $foreman1      = $first(['nikforeman1']);
        $foreman1Name  = $first(['namaforeman1']);
        $foreman2      = $first(['nikforeman2']);
        $foreman2Name  = $first(['namaforeman2']);
        $spv1          = $first(['nikspv1']);
        $spv1Name      = $first(['namaspv1']);
        $spv2          = $first(['nikspv2']);
        $spv2Name      = $first(['namaspv2']);
        $manager       = $first(['nikmanager']);
        $managerName   = $first(['namamanager']);
        $foremanReason = $first(['foreman_reason']);
        $spvReason     = $first(['spv_reason']);
        $managerReason = $first(['manager_reason']);

        return [
            'id'                        => $id,
            'nama_line'                 => $namaLine,
            'nama_member'               => $namaMember,
            'leader_approve_pe'         => $leaderStatus,
            'tanggal_approve_leader_pe' => $leaderTgl,
            'detail_ref'                => $detailRef,
            'nikforeman1'               => $foreman1,
            'namaforeman1'              => $foreman1Name,
            'nikforeman2'               => $foreman2,
            'namaforeman2'              => $foreman2Name,
            'nikspv1'                   => $spv1,
            'namaspv1'                  => $spv1Name,
            'nikspv2'                   => $spv2,
            'namaspv2'                  => $spv2Name,
            'nikmanager'                => $manager,
            'namamanager'               => $managerName,
            'foreman_reason'            => $foremanReason,
            'spv_reason'                => $spvReason,
            'manager_reason'            => $managerReason,
            'status'                    => $this->statusApproval(
                $foreman1 ?? $foreman2,
                $spv1 ?? $spv2,
                $manager,
                $foremanReason,
                $spvReason,
                $managerReason
            ),
        ];
    }

    private function dateValueIsEmpty(mixed $v): bool
    {
        if ($v === null) {
            return true;
        }
        if ($v === '') {
            return true;
        }
        if (is_string($v) && trim($v) === '') {
            return true;
        }

        return false;
    }

    private function textValueIsFilled(mixed $v): bool
    {
        if ($v === null) {
            return false;
        }
        if (is_string($v) && trim($v) !== '') {
            return true;
        }
        if (!is_string($v) && $v !== '') {
            return true;
        }

        return false;
    }

    private function statusApproval(
        mixed $foreman1,
        mixed $spv1,
        mixed $manager,
        mixed $foremanReason = null,
        mixed $spvReason     = null,
        mixed $managerReason = null,
    ): string {
        if ($this->textValueIsFilled($managerReason)) {
            return self::STATUS_REJECTED_MANAGER;
        }
        if ($this->textValueIsFilled($spvReason)) {
            return self::STATUS_REJECTED_SPV;
        }
        if ($this->textValueIsFilled($foremanReason)) {
            return self::STATUS_REJECTED_FOREMAN;
        }

        $f = $this->textValueIsFilled($foreman1);
        $s = $this->textValueIsFilled($spv1);
        $m = $this->textValueIsFilled($manager);

        if ($f && $s && $m) {
            return self::STATUS_APPROVED;
        }
        if ($f && $s && !$m) {
            return self::STATUS_MANAGER;
        }
        if ($f && !$s && !$m) {
            return self::STATUS_SPV;
        }
        if (!$f && !$s && !$m) {
            return self::STATUS_FOREMAN;
        }

        return self::STATUS_FOREMAN;
    }

    public function shouldShowApproveRejectButtons(): bool
    {
        if ($this->level === null) {
            return false;
        }
        if ($this->level === self::STATUS_APPROVED) {
            return false;
        }
        if (
            in_array($this->level, [
                self::STATUS_REJECTED_FOREMAN,
                self::STATUS_REJECTED_SPV,
                self::STATUS_REJECTED_MANAGER,
            ], true)
        ) {
            return false;
        }

        return in_array($this->level, [
            self::STATUS_FOREMAN,
            self::STATUS_SPV,
            self::STATUS_MANAGER,
        ], true);
    }

    public function previewDetailPdf($namaLine)
    {
        $this->detailLine = $namaLine;
        $data             = DB::connection('tenken')
            ->table('moni_chk_circuit as m')
            ->selectRaw("m.item_pengecekan, m.metode_pengecekan, m.kriteria, ISNULL(h.hasil,'') AS hasil, ISNULL(h.keterangan,'') AS keterangan")
            ->leftJoin('moni_history as h', function ($join) use ($namaLine) {
                $join
                    ->on('m.item_pengecekan', '=', 'h.item_pengecekan')
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

        $fileName = 'detail_' . $this->generateFN();
        $folder   = 'public/tenken/pdf/detail';

        if (!Storage::exists($folder)) {
            Storage::makeDirectory($folder);
        }

        $path = $folder . '/' . $fileName;
        Storage::put($path, $mpdf->Output('', 'S'));

        $this->pdfPath = Storage::url($path) . '?v=' . time();

        $this->dispatch('open-pdf-modal');
    }

    private function generateFN(): string
    {
        if ($this->detailLine !== '') {
            return 'tenken_circuit_details_' . $this->mesin . '_' . $this->tanggal . '_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $this->detailLine) . '.pdf';
        }

        return 'tenken_circuit_' . $this->mesin . '_' . $this->tanggal . '.pdf';
    }

    public function approveByLevel(string $level): void
    {
        if (!$this->shouldShowApproveRejectButtons()) {
            return;
        }
        $level = strtolower(trim($level));
        if ($level === self::STATUS_APPROVED) {
            return;
        }
        if (!in_array($level, [self::STATUS_FOREMAN, self::STATUS_SPV, self::STATUS_MANAGER], true)) {
            return;
        }
        if ($this->dataIds === []) {
            $this->notifyActionError('Tidak ada id baris untuk di-update.');

            return;
        }

        $colUpdate = match ($level) {
            self::STATUS_FOREMAN => [
                'nikforeman1'  => self::FOREMAN_NIK[0]['nik'],
                'nikforeman2'  => self::FOREMAN_NIK[1]['nik'],
                'namaforeman1' => self::FOREMAN_NIK[0]['nama'],
                'namaforeman2' => self::FOREMAN_NIK[1]['nama'],
            ],
            self::STATUS_SPV     => [
                'nikspv1'  => self::SPV_NIK[0]['nik'],
                'nikspv2'  => self::SPV_NIK[1]['nik'],
                'namaspv1' => self::SPV_NIK[0]['nama'],
                'namaspv2' => self::SPV_NIK[1]['nama'],
            ],
            self::STATUS_MANAGER => [
                'nikmanager'  => self::MANAGER_NIK[0]['nik'],
                'namamanager' => self::MANAGER_NIK[0]['nama'],
            ],
            default              => null,
        };

        try {
            DB::connection('tenken')
                ->table('moni_approval')
                ->whereIn('id', $this->dataIds)
                ->update($colUpdate);
        } catch (\Throwable $e) {
            $this->notifyActionError($e->getMessage());

            return;
        }

        $this->redirectToTenkenView();
    }

    public function rejectByLevel(string $level): void
    {
        if (!$this->shouldShowApproveRejectButtons()) {
            return;
        }
        $level = strtolower(trim($level));
        if (!in_array($level, [self::STATUS_FOREMAN, self::STATUS_SPV, self::STATUS_MANAGER], true)) {
            return;
        }
        $reason = trim($this->rejectReason);
        if ($reason === '') {
            $this->notifyActionError('Isi alasan penolakan.');

            return;
        }
        if ($this->dataIds === []) {
            $this->notifyActionError('Tidak ada id baris untuk di-update.');

            return;
        }

        $colReason = $level . '_reason';

        try {
            DB::connection('tenken')
                ->table('moni_approval')
                ->whereIn('id', $this->dataIds)
                ->update([
                    $colReason => $reason,
                ]);
        } catch (\Throwable $e) {
            $this->notifyActionError($e->getMessage());

            return;
        }

        $this->rejectReason = '';
        $this->redirectToTenkenView();
    }

    private function notifyActionError(string $message): void
    {
        $this->dispatch('circuit-action-error', ['message' => $message]);
    }

    private function redirectToTenkenView(): void
    {
        $this->redirect(route('tenken.circuit.view', ['tanggal' => $this->tanggal]), navigate: true);
    }

    public function render()
    {
        return view($this->pdfOnly ? 'livewire.tenken.circuit-pdf-only' : 'livewire.tenken.circuit');
    }
}
