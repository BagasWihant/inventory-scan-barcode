<?php

namespace App\Livewire\Menu;

use App\Models\MaterialRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class MonitoringBomRequest extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $time, $totalCount;
    public $material;
    public $dateFilter;
    public $line_code;

    public function mount()
    {
        $this->dateFilter = date('Y-m-d');
    }


    public function refreshTable()
    {
        // === 1) Ambil MPS & kunci-kunci ===
        $mps = DB::table('mps')
            ->when($this->dateFilter, fn($q) => $q->whereDate('plan_issue_dt', $this->dateFilter))
            ->when($this->line_code, fn($q) => $q->where('line_c', $this->line_code))
            ->get()
            ->map(function ($r) {            // pastikan product_no bersih untuk join
                $r->product_no = trim($r->product_no ?? '');
                return $r;
            });

        $kitNos = $mps->pluck('kit_no')->unique()->values();

        // === 2) Detail MPS & In-Stock, keyed by kit_no ===
        $detailsByKit = DB::table('mps_detail')
            ->whereIn('kit_no', $kitNos)
            ->get()
            ->groupBy('kit_no');

        $instockByKit = DB::table('material_in_stock')
            ->whereIn('kit_no', $kitNos)
            ->when($this->line_code, fn($q) => $q->where('line_c', $this->line_code))
            ->get()
            ->groupBy('kit_no');

        // === 3) PO KIAS (index: no_model -> first row) ===
        $po_kias = DB::connection('server_1')->table('po_kias')
            ->when($this->dateFilter, fn($q) => $q->whereDate('issue_date', $this->dateFilter))
            ->when($this->line_code, fn($q) => $q->where('line_code', $this->line_code))
            ->get()
            ->map(function ($x) {
                $x->no_model = trim($x->no_model ?? '');
                return $x;
            });

        // Index: model -> (no_po, qty_request) yang pertama (atau apa pun logikamu)
        $poIndex = $po_kias
            ->groupBy('no_model')
            ->map(function ($g) {
                $f = $g->first();
                return (object) [
                    'no_po' => $f->no_po ?? null,
                    'qty_request' => isset($f->qty_request) ? (float) $f->qty_request : null,
                ];
            });

        // === 4) BOM, keyed by product_no (trim) ===
        $no_model = $po_kias->pluck('no_model')->unique()->values();
        $bomByProduct = DB::connection('server_2')->table('bom')
            ->whereIn('product_no', $no_model)
            ->get()
            ->map(function ($i) {
                $i->product_no = trim($i->product_no ?? '');
                return $i;
            })
            ->groupBy('product_no');

        // === 5) Susun baris hasil (ARRAY murni buat Livewire) ===
        $material = $mps->map(function ($r) use ($detailsByKit, $instockByKit, $bomByProduct, $poIndex) {
            $prod = $r->product_no;
            $kit = $r->kit_no;

            $po = $poIndex->get($prod);                  // object kecil (no_po, qty_request) atau null
            $bom = $bomByProduct->get($prod, collect());  // Collection of stdClass
            $det = $detailsByKit->get($kit, collect());
            $stock = $instockByKit->get($kit, collect());

            return [
                // kolom mps (pakai apa yang kamu butuh)
                'product_no' => $prod,
                'kit_no' => $kit,
                'line_c' => $r->line_c,
                'plan_issue_dt' => $r->plan_issue_dt,
                'entry_dt' => $r->entry_dt,

                // PO ringkas: hanya yang kamu perlukan di blade
                'no_po' => $po ? [
                    'no_po' => $po->no_po,
                    'qty_request' => $po->qty_request,
                ] : null,

                // Hasil tabel (array murni, bukan Collection/object)
                'detail' => $det->map(fn($x) => (array) $x)->values()->all(),
                'instock' => $stock->map(fn($x) => (array) $x)->values()->all(),
                'po_kias' => $bom->map(fn($x) => (array) $x)->values()->all(), // ini sebenernya BOM list
            ];
        })->values()->all();

        // === 6) Assign ke Livewire ===
        $this->material = $material;       // array murni
        $this->totalCount = count($material);
    }

    public function render()
    {
        $this->time = now();
        $this->refreshTable();
        return view('livewire.menu.monitoring-bom-request');
    }
}
