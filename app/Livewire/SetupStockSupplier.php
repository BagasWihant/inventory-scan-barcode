<?php

namespace App\Livewire;

use App\Exports\ReceivingSupplierNotAssyReport;
use App\Exports\ReceivingSupplierReport;
use App\Models\itemIn;
use App\Models\PaletRegister;
use App\Models\PaletRegisterDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;


class SetupStockSupplier extends Component
{
    use WithPagination;
    public $searchPalet, $listPallet, $input_setup_by, $scan_date_modal, $no_palet_modal,$status=null,$lokasi='-';
    public  $listMaterialDetail = [], $paletData,$palet,$updateQ;
    public $paletDisable = false;
    public $dateStart,$dateEnd;


     public function detail($palet)
    {
        $this->listMaterialDetail = DB::table('palet_register_details as a')->where('palet_no', $palet)
            ->selectRaw('material_no as material,sum(a.qty) as counter,count(*) as pack, material_name as matl_nm')->groupBy(['material_no', 'material_name'])->get();
        $this->no_palet_modal = $palet;
        $this->paletData = PaletRegister::where('palet_no', $palet)->first();
        $this->scan_date_modal = date('d-m-Y H:i:s', strtotime($this->paletData->created_at));
        // $this->dispatch('showModal');
        // dd($this->listMaterialDetail);
    }

    public function editLokasi($palet) {
        $this->palet = $palet;
        $this->updateQ = PaletRegister::where('palet_no', $palet)->first();
        $this->dispatch('swalEditLokasi',['data'=>$this->updateQ]);
    }

    #[On('savingUpdateLokasi')]
    public function savingUpdateLokasi($lokasi){
        $this->updateQ->lokasi = $lokasi;
        $this->updateQ->save();
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
        $listPalet = DB::table('palet_registers')->where('is_done', 1)
            ->when($this->searchPalet, fn($q) => $q->where('palet_no', 'like', '%' . $this->searchPalet . '%'))
            ->when($this->status, fn($q) => $q->where('status',  $this->status == 'supply' ? 1 : 0 ))
            ->when($this->lokasi != '-', fn($q) => $q->where('lokasi',  $this->lokasi))
            ->when($this->dateStart, fn($q) => $q->where('issue_date','>=',$this->dateStart))
            ->when($this->dateEnd, fn($q) => $q->where('issue_date','<=',$this->dateEnd))
            ->paginate(25);
        return view('livewire.setup-stock-supplier', ['listMaterial' => $listPalet]);
    }
}
