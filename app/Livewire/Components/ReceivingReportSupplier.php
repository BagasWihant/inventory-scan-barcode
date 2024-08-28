<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReceivingReportSupplier extends Component
{
    public $kitNo, $paletNo, $materialCodeSupp, $dateStart, $dateEnd, $suratJalan;
    public $listPalet = [], $listPaletNoSup = [], $listMaterial = [], $receivingData = [], $listSuratJalan = [];
    public $clearButton = false;

    public function updated($prop)
    {
        switch ($prop) {
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
            case 'suratJalan':
                $distinc = DB::table('material_in_stock')
                    ->where('kit_no', '!=', '')
                    ->where('surat_jalan', $this->suratJalan)
                    ->select('kit_no as pallet_no')->distinct();
                $this->listPalet = $distinc->pluck('pallet_no')->all();
                break;
            default:
                return;
                break;
        }
    }

    public function showData()
    {
        $this->clearButton = true;
        if (!$this->dateStart && !$this->dateEnd) {
            $this->dateStart = '2023-01-01';
            $this->dateEnd = date('Y-m-d');
        }

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

    public function resetData()
    {
        $this->receivingData = [];
        $this->materialCodeSupp = "";
        $this->dateStart = "";
        $this->paletNo = "";
        $this->kitNo = "";
        $this->suratJalan = "";
        $this->dateEnd = "";
        $this->clearButton = false;
        $this->mount();
    }

    public function mount()
    {
        $distinc = DB::table('material_in_stock')->where('kit_no', '!=', '')->select('kit_no as pallet_no')->distinct();
        $this->listPalet = $distinc->pluck('pallet_no')->all();

        $distincSJ = DB::table('material_in_stock')->where('surat_jalan', '!=', '')->select('surat_jalan')->distinct();
        $this->listSuratJalan = $distincSJ->pluck('surat_jalan')->all();
    }

    public function render()
    {
        return view('livewire.components.receiving-report-supplier');
    }
}
