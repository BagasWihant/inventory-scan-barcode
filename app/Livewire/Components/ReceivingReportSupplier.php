<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReceivingReportSupplier extends Component
{
    public $kitNo, $paletNo, $materialCode, $dateStart, $dateEnd, $suratJalan;
    public $listPalet = [], $listPaletNoSup = [], $listMaterial = [], $receivingData = [], $listSuratJalan = [];
    public $clearButton = false, $suratJalanDisable = false, $kitNoDisable = false, $paletNoDisable = false,$materialCodeDisable=false;

    public function updated($prop)
    {
        switch ($prop) {
            case 'kitNo':

                $distinc = DB::table('material_in_stock')
                    ->where('kit_no', 'like', '%' . $this->kitNo . '%')
                    ->when($this->suratJalan, function ($query) {
                        return $query->where('surat_jalan', $this->suratJalan);
                    })
                    ->select('kit_no as pallet_no')->distinct()->limit(10);
                $this->listPalet = $distinc->pluck('pallet_no')->all();
                break;

            case 'paletNo':

                $distinc = DB::table('material_in_stock')->select('pallet_no')
                ->where('kit_no', $this->kitNo)
                ->where('pallet_no', 'like', '%' . $this->paletNo . '%')
                ->distinct()->limit(10);
                $this->listPaletNoSup = $distinc->pluck('pallet_no')->all();

                break;
            case 'suratJalan':
                $distincSJ = DB::table('material_in_stock')
                    ->where('surat_jalan', 'like', '%' . $this->suratJalan . '%')
                    ->select('surat_jalan')->distinct()->limit(10);
                $this->listSuratJalan = $distincSJ->pluck('surat_jalan')->all();

                break;
            case 'materialCode':
               
                $distinc = DB::table('material_in_stock')->select('material_no')
                ->where('kit_no', $this->kitNo)
                ->where('pallet_no', $this->paletNo)
                ->where('material_no', 'like', '%' . $this->materialCode . '%')
                ->distinct()->limit(10);
                $this->listMaterial = $distinc->pluck('material_no')->all();

                break;
            default:
                return;
                break;
        }
    }

    public function chooseSuratJalan($palet)
    {
        $this->suratJalan = $palet;
        $this->suratJalanDisable = true;
        $this->clearButton = true;
        $this->listSuratJalan =  'kosong';
    }

    public function chooseKitNo($kit)
    {
        $this->kitNo = $kit;
        $this->kitNoDisable = true;
        $this->clearButton = true;
        $this->suratJalanDisable = true;
        $this->listPalet = 'kosong';
    }


    public function choosePalet($palet) {
        $this->paletNo = $palet;
        $this->paletNoDisable = true;
        $this->clearButton = true;
        $this->listPaletNoSup = 'kosong';
    }

    public function chooseMaterial($mat) {
        $this->materialCode = $mat;
        $this->materialCodeDisable = true;
        $this->clearButton = true;
        $this->listMaterial = 'kosong';
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
            $this->materialCode ?? "",
        ];

        $this->receivingData = DB::select('EXEC sp_Receiving_report_supplier ?,?,?,?,?,?', $data);
    }

    public function resetData()
    {
        $this->receivingData = [];
        $this->materialCode = "";
        $this->dateStart = "";
        $this->paletNo = "";
        $this->kitNo = "";
        $this->suratJalan = "";
        $this->dateEnd = "";
        $this->clearButton = false;
        $this->suratJalanDisable = false;
        $this->kitNoDisable = false;
        $this->paletNoDisable = false;
    }


    public function render()
    {
        return view('livewire.components.receiving-report-supplier');
    }
}
