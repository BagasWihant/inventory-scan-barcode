<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\ScannedExport;
use App\Models\abnormalMaterial;
use App\Models\itemIn;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

// misal edit kan enek scan 2 kui pie
// karo cek sing insert new 

class ReceivingSIWSNews extends Component
{
    use WithPagination;

    private $tableSetupMst = 'material_setup_mst';
    private $userId;
    protected $sws_code;
    protected $paletBarcode;
    protected $produkBarcode;
    protected $trucking_id;


    public function mount()
    {
        $this->userId = auth()->user()->id;
    }

    public function render()
    {
        return view('livewire.receiving-s-i-w-s-news');
    }


    public function paletBarcodeScan($key)
    {
        $this->paletBarcode = substr($key, 0, 10);

        $truk = DB::table($this->tableSetupMst)->where('pallet_no', $this->paletBarcode)->select('kit_no')->first();
        if ($truk) {
            $this->trucking_id = $truk->kit_no;

            $data = $this->refreshData();
            if (!empty($data[0])) {
                return [
                    'success' => true,
                    'material' => $data[0],
                    'trucking_id' => $truk->kit_no
                ];
            } elseif (empty($data[0]) && !empty($data[1])) {
                return [
                    'success' => true,
                    'material' => [],
                    'trucking_id' => $truk->kit_no,
                    'message' => 'Scan Confirmed'
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Pallet Tidak Ditemukan'
        ];
    }

    private function refreshData()
    {
        $getScanned =  DB::table('material_in_stock')->select('material_no')
            ->where('pallet_no', $this->paletBarcode)
            ->union(DB::table('abnormal_materials')->select('material_no')->where('pallet_no', $this->paletBarcode))
            ->pluck('material_no')
            ->all();

        // $firstText = strtolower(substr($this->paletBarcode, 0, 1));
        // $allowText = ['m', 'c'];
        $pattern = '/^\d{2}-\d{4}$/';

        if (preg_match($pattern, $this->paletBarcode)) {

            $getall =  DB::table($this->tableSetupMst . ' as  a')
                ->selectRaw('a.kit_no, pallet_no, r.material_no,count(a.material_no) as pax, sum(a.picking_qty) as picking_qty, min(a.serial_no) as serial_no,line_c,serial_no,l.location_cd,a.plan_issue_dt_from')
                ->leftJoin('material_mst as b', 'a.serial_no', '=', 'b.matl_no')
                ->leftJoin('master_wire_register as r', 'a.material_no', '=', 'r.id')
                ->leftJoin('matloc_temp_CNCKIAS2 as l', 'a.material_no', '=', 'l.material_no')
                ->where('a.pallet_no', $this->paletBarcode)
                ->whereNotIn('r.material_no', $getScanned)
                ->groupBy('a.pallet_no', 'r.material_no', 'line_c', 'r.material_no', 'serial_no', 'a.kit_no', 'l.location_cd', 'a.plan_issue_dt_from')
                ->orderByRaw('max(scanned_at) DESC')
                ->orderByDesc('r.material_no')->get();
        } else {
            $getall = DB::table($this->tableSetupMst . ' as  a')
                ->selectRaw('a.kit_no, pallet_no, a.material_no ,count(a.material_no) as pax, sum(a.picking_qty) as picking_qty, min(a.serial_no) as serial_no,line_c,l.location_cd,a.plan_issue_dt_from ')
                ->leftJoin('material_mst as b', 'a.serial_no', '=', 'b.matl_no')
                ->leftJoin('matloc_temp_CNCKIAS2 as l', 'a.material_no', '=', 'l.material_no')
                ->where('a.pallet_no', $this->paletBarcode)
                ->whereNotIn('a.material_no', $getScanned)
                ->groupBy('a.pallet_no', 'a.material_no', 'line_c', 'a.kit_no', 'l.location_cd', 'a.plan_issue_dt_from')
                ->orderByRaw('max(scanned_at) DESC')
                ->orderByDesc('a.material_no')->get();
        }

        if ($getall->count() === 0) {
            $instock = DB::table('material_in_stock')->select('material_no')
                ->where('pallet_no', $this->paletBarcode)
                ->union(DB::table('abnormal_materials')->select('material_no')->where('pallet_no', $this->paletBarcode))
                ->pluck('material_no')
                ->count();
            return [null, $instock];
        }

        return [$getall, null];
    }

    public function confirm($req)
    {
        $obj = collect($req)->map(fn($item) => (object) $item);
        foreach ($obj as $v) {
            if ($v->counter > 0) {
                if ($v->counter > $v->picking_qty) {
                    abnormalMaterial::create([
                        'pallet_no' => $this->paletBarcode,
                        'material_no' => $v->material,
                        'picking_qty' => $v->counter - $v->picking_qty,
                        'locate' => $v->location_cd,
                        'trucking_id' => $this->trucking_id,
                        'user_id' => $this->userId,
                        'kit_no' => $v->kit_no,
                        'line_c' => $v->line_c,
                        'status' => 1
                    ]);
                } else if ($v->counter < $v->picking_qty) {
                    abnormalMaterial::create([
                        'pallet_no' => $this->paletBarcode,
                        'material_no' => $v->material,
                        'picking_qty' => $v->picking_qty - $v->counter,
                        'locate' => $v->location_cd,
                        'trucking_id' => $this->trucking_id,
                        'user_id' => $this->userId,
                        'kit_no' => $v->kit_no,
                        'line_c' => $v->line_c,
                        'status' => 0
                    ]);
                } else {
                    itemIn::create([
                        'pallet_no' => $this->paletBarcode,
                        'material_no' => $v->material,
                        'picking_qty' => $v->counter,
                        'locate' => $v->location_cd,
                        'trucking_id' => $this->trucking_id,
                        'line_c' => $v->line_c,
                        'kit_no' => $v->kit_no,
                        'user_id' => $this->userId
                    ]);
                }
            }
        }

        return Excel::download(new ScannedExport($obj), "Scanned Items_" . $this->paletBarcode . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }
}
