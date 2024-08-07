<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class CheckingStock extends Component
{
    public $paletBarcode = "", $materialCode = "", $dateStart = "", $dateEnd = "", $kitNo = "", $paletNo="",$materialCodeSupp="";
    public $receivingData = [], $inStock = [], $listMaterial = [], $listPalet = [], $listPaletNoSup = [];
    public $mode = null;

    public function updated($prop)
    {
        switch ($prop) {
            case 'paletBarcode':
                $this->paletBarcode = substr($this->paletBarcode, 0, 10);

                $distinc = DB::table('material_setup_mst_CNC_KIAS2')->select('material_no')->where('pallet_no', $this->paletBarcode)->distinct();
                $this->listMaterial = $distinc->pluck('material_no')->all();
                break;
            case 'kitNo':

                $this->kitNo = substr($this->kitNo, 0, 10);

                $distinc = DB::table('material_in_stock')->select('material_no')->where('kit_no', $this->kitNo)->distinct();
                $this->listMaterial = $distinc->pluck('material_no')->all();

                $distinc = DB::table('material_in_stock')->select('pallet_no')->where('kit_no', $this->kitNo)->distinct();
                $this->listPaletNoSup = $distinc->pluck('pallet_no')->all();
                break;

            case 'paletNo':
                $this->materialCodeSupp = "";

                $distinc = DB::table('material_in_stock')->select('material_no')->where('kit_no', $this->kitNo)->where('pallet_no', $this->paletNo)->distinct();
                $this->listMaterial = $distinc->pluck('material_no')->all();
                break;

            default:
                return;
                break;
        }
    }

    public function changeReceivingMode($mode)
    {
        $this->resetData();
        $this->mode = $mode;
        if ($this->mode == 'cnc') {
            $distinc = DB::table('material_setup_mst_CNC_KIAS2')->select('pallet_no')->distinct();
            $this->listPalet = $distinc->pluck('pallet_no')->all();
        } else {
            $distinc = DB::table('material_in_stock')->where('kit_no', '!=', '')->select('kit_no as pallet_no')->distinct();
            $this->listPalet = $distinc->pluck('pallet_no')->all();
        }
    }

    // public function materialChange()
    // {
    //     $this->materialCode =  $this->materialCode == "" ? null : $this->materialCode;
    // }

    public function resetData()
    {
        $this->receivingData = [];
        $this->paletBarcode = "";
        $this->materialCode = "";
        $this->materialCodeSupp = "";
        $this->dateStart = "";
        $this->paletNo = "";
        $this->kitNo = "";
        $this->dateEnd = "";
    }
    public function showData($mode)
    {
        if (!$this->dateStart && !$this->dateEnd) {
            $this->dateStart = '2023-01-01';
            $this->dateEnd = date('Y-m-d');
        }
        if ($mode === 'cnc') {

            $this->receivingData = DB::select('EXEC sp_Receiving_report ?,?,?,?,?,?,?', [
                'detail',
                $this->paletBarcode ?? "",
                $this->dateStart ?? '',
                $this->dateEnd ?? "",
                '',
                $this->materialCode ?? "",
                '',
            ]);
        } else {
            $data = [
                'detail',
                $this->kitNo ?? "",
                $this->dateStart ?? '',
                $this->dateEnd ?? "",
                $this->paletNo ?? "",
                $this->materialCodeSupp ?? "",
            ];
            
            $this->receivingData = DB::select('EXEC sp_Receiving_report_supplier ?,?,?,?,?,?', $data);
        }
    }


    public function render()
    {

        return view('livewire.checking-stock', [
            // 'listPalet' => $listPalet,
            // 'listMaterial' => $listMaterial
        ]);
    }
}
