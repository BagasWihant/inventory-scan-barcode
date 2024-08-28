<?php

namespace App\Livewire;

use App\Exports\ReceivingSupplierNotAssyReport;
use App\Exports\ReceivingSupplierReport;
use App\Models\itemIn;
use App\Models\PaletRegister;
use App\Models\PaletRegisterDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class SetupStockSupplier extends Component
{
    public $searchPalet, $listPallet, $input_setup_by, $scan_date_modal, $no_palet_modal;
    public $listMaterial = [], $listMaterialDetail=[], $paletData;
    public $paletDisable = false;


    public function detail($palet)
    {
        $this->listMaterialDetail = DB::table('palet_register_details as a')->where('palet_no', $palet)
        ->selectRaw('material_no as material,sum(a.qty) as counter,count(*) as pack, material_name as matl_nm')->groupBy(['material_no', 'material_name'])->get();
        $this->no_palet_modal = $palet;
        $this->paletData = PaletRegister::where('palet_no', $palet)->first();
        $this->scan_date_modal = date('d-m-Y H:i:s', strtotime($this->paletData->created_at));
    }
   
    
    public function print()
    {

        $dataPrint = [
            'data' => $this->listMaterialDetail,
            'palet_no' => $this->no_palet_modal,
            'issue_date' => $this->paletData->issue_date,
            'line_c' => $this->paletData->line_c
        ];
        return Excel::download(new ReceivingSupplierReport($dataPrint), "Register Palet_" . $dataPrint['palet_no'] . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }
    public function render()
    {
        $this->listMaterial = PaletRegister::where('is_done', 1)->get();

        return view('livewire.setup-stock-supplier');
    }
}
