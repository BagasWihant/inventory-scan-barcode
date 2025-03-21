<?php

namespace App\Livewire;

use App\Exports\ExportStockTakingExcel;
use App\Models\PaletRegister;
use App\Models\PaletRegisterDetail;
use App\Models\StockTakingCot as ModelsStockTakingCot;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class StockTakingCot extends Component
{
    public $tglSto;
    public $userId;
    public $noSto;
    public $materialNo;
    public $noPalet;
    public $partial;
    public $dataTable = [];
    public $selectedMaterial;


    public function mount()
    {
        $this->userId = auth()->user()->id;
        $qryNoSto = DB::table('WH_config')->where('config', 'noStockCot')->select('config', 'value')->get()->keyBy('config');
        $today = date('dmy');
        $noSto = substr((int)$qryNoSto['noStockCot']->value, 0, 6);
        if ($noSto == $today) {
            $urut = (int)substr($qryNoSto['noStockCot']->value, 6, 3);
            $this->noSto = 'STO-COT-' . $noSto . str_pad($urut + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $this->noSto = 'STO-COT-' . $today . '001';
        }
        $this->loadData();
    }

    public function materialNoScan()
    {
        if ($this->tglSto == null) {
            return $this->dispatch('notification', ['time' => 3500, 'icon' => 'warning', 'title' => 'Please choose date']);
        }
        $qrtrim = trim(str_replace(" ", "", $this->materialNo));
        if (strpos($qrtrim, "//")) {

            $split = explode("//", $qrtrim);
            if (count($split) < 2) {
                $this->materialNo = null;
                return;
            }
            $split1 = explode("-", $split[0]);

            $materialNoParse = $split1[0];
            $qtyParse = preg_replace('/[^0-9]/', '', $split1[2]);
            $lineParse = "";
            $kitNo  = "";

        } else {
            $split = explode("/", $qrtrim);
            if (count($split) < 2) {
                $this->materialNo = null;
                return;
            }
            $qtyParse = substr($split[1], 1, 4);

            $hrfBkg = substr($split[1], -9);
            $hrfDpn = substr($split[1], 0, 5);

            $hapusdepan = str_replace($hrfDpn, "", $split[1]);
            $materialNoParse = str_replace($hrfBkg, "", $hapusdepan);

            $lineParse = trim($split[2]);
            $kitNo  = str_replace('-','',trim($split[0]));

        }
        $supplierCode = DB::table('material_setup_mst_supplier as m')
            ->where('supplier_code', $materialNoParse)
            ->where('kit_no',$kitNo)
            ->where('line_c',$lineParse)
            ->leftJoin('material_conversion_mst as c', 'm.material_no', '=', 'c.sws_code')
            ->leftJoin('material_mst as mst', 'm.material_no', '=', 'mst.matl_no')
            ->select('m.line_c', 'm.material_no', 'picking_qty', 'mst.matl_nm')->first();

        if ($this->checkDouble(materialNo: ['no' => $supplierCode->material_no, 'line' => $lineParse])) {
            $this->materialNo = null;
            return $this->dispatch('notification', ['time' => 2500, 'icon' => 'warning', 'title' => 'Material sudah ada di list']);
        }
        
        ModelsStockTakingCot::create([
            'no_sto' => $this->noSto,
            'material_no' => $supplierCode->material_no,
            'material_name' => $supplierCode->matl_nm,
            'line_code' => $lineParse,
            'qty' => $qtyParse,
            'picking_qty' => $supplierCode->picking_qty,
            'tgl_sto' => $this->tglSto,
            'user_id' => $this->userId
        ]);

        $this->materialNo = null;
        $this->loadData();
    }

    public function noPaletScan()
    {
        if($this->noPalet == null) return;
        
        if ($this->tglSto == null) {
            return $this->dispatch('notification', ['time' => 3500, 'icon' => 'warning', 'title' => 'Please choose date']);
        }

        $palet = DB::table('palet_register_details as d')
            ->leftJoin('palet_registers as p', 'd.palet_no', '=', 'p.palet_no')
            ->leftJoin('material_mst as m', 'd.material_no', '=', 'm.matl_no')
            ->where('p.palet_no', $this->noPalet)
            ->where('d.is_done', '1')
            ->select('d.material_no', 'd.qty', 'd.material_name', 'p.line_c', 'd.palet_no', 'm.matl_nm', 'p.lokasi')->get();

        if (count($palet) == 0) {
            $this->noPalet = null;
            return $this->dispatch('notification', ['time' => 2500, 'icon' => 'warning', 'title' => 'Palet belum selesai / tidak ada']);
        }
        if ($this->checkDouble(paletNo: $palet[0]->palet_no)) {
            $this->noPalet = null;
            return $this->dispatch('notification', ['time' => 2500, 'icon' => 'warning', 'title' => 'Material sudah ada di list']);
        }
        foreach ($palet as $item) {
            ModelsStockTakingCot::create([
                'no_sto' => $this->noSto,
                'material_no' => $item->material_no,
                'material_name' => $item->matl_nm,
                'line_code' => str_replace(' ', '', $item->line_c),
                'qty' => $item->qty,
                'palet_no' => $item->palet_no,
                'location' => $item->lokasi,
                'tgl_sto' => $this->tglSto,
                'user_id' => $this->userId
            ]);
        }
        $this->loadData();
        $this->noPalet = null;
    }

    public function deleteItem($id)
    {
        ModelsStockTakingCot::where('id', $id)->delete();
        $this->loadData();
        return ['success' => true];
    }

    public function changeLocation($newLocation,$qty)
    {
        
        ModelsStockTakingCot::where('no_sto', $this->noSto)
            ->where('material_no', $this->selectedMaterial['material_no'])
            ->update([
                'location' => $newLocation,
                'qty' => $qty
            ]);
        $this->loadData();
    }

    public function clearData()
    {
        ModelsStockTakingCot::where('no_sto', $this->noSto)->delete();
        $this->loadData();
    }

    public function saveAll()
    {
        $data = ModelsStockTakingCot::where('no_sto', $this->noSto);
        $data->update(['status' => '1']);
        DB::table('WH_config')->where('config', 'noStockCot')->select('config', 'value')->update(['value' => substr($this->noSto, 8)]);


        $this->mount();
        return Excel::download(new ExportStockTakingExcel($data->get()), "Stock Taking COT" . $this->noSto . "_" . date('YmdHis') . ".xlsx", \Maatwebsite\Excel\Excel::XLSX);
    }

    public function render()
    {
        return view('livewire.stock-taking-cot');
    }

    private function loadData()
    {
        $this->dataTable = ModelsStockTakingCot::where('no_sto', $this->noSto)
            ->where('status', '0')->get();
    }

    private function checkDouble($materialNo = null, $paletNo = null)
    {
        return ModelsStockTakingCot::where('no_sto', $this->noSto)
            ->when($materialNo, function ($query) use ($materialNo) {
                return $query->where('material_no',  $materialNo['no'])
                    ->where('line_code',  $materialNo['line']);
            })
            ->when($paletNo != null, function ($query) use ($paletNo) {
                return $query->where('palet_no',  $paletNo);
            })->where(DB::raw('CONVERT(date, created_at)'), now()->format('Y-m-d'))
            ->exists();
    }
}
