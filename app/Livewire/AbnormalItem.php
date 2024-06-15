<?php

namespace App\Livewire;

use Livewire\Component;
use App\Exports\InStockExport;
use App\Exports\InStockExportExcel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AbnormalItem extends Component
{
    public $dataCetak, $searchKey, $status;
    public function render()
    {
        $query = DB::table('abnormal_materials')
            ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax,trucking_id,locate,status')
            ->groupBy(['material_no', 'pallet_no','trucking_id','locate','status']);

        if ($this->status !== null && $this->status !== '-') $query->where('status', $this->status);

        $query->where(function ($query) {
            $query->where('pallet_no', 'like', "%$this->searchKey%")->orWhere('material_no', 'like', "%$this->searchKey%");
        });

        $data = $query->get();
        $dev['a'] = $query->toRawSql();
        $dev['k']=$this->status;

        if ($this->searchKey) $this->dispatch('searchFocus');

        $this->dataCetak = $data;
        return view('livewire.abnormal-item', [
            'data' => $data,
            'dev' => $dev,
            
        ]);
    }

    public function statusChange()
    {
    }

    public function konfirmasi($id)
    {
        $split = explode("|", $id);
        $dataDetail = DB::table('abnormal_materials')
        ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax')
        ->groupBy(['material_no', 'pallet_no'])
        ->where('pallet_no', $split[0])
        ->where('material_no', $split[1]);
        // dump($dataDetail->first());
        $data = $dataDetail->first();
        $this->dispatch('modalConfirm', $data->qty);

    }

    public function exportPdf()
    {
        if ($this->searchKey) $name = "Kurang_" . $this->searchKey . "-" . date('Ymd') . ".pdf";
        else $name = "Kurang-" . date('Ymd') . ".pdf";

        return Excel::download(new InStockExport($this->dataCetak), $name, \Maatwebsite\Excel\Excel::MPDF);
    }

    public function exportExcel()
    {
        if ($this->searchKey) $name = "Kurang_" . $this->searchKey . "-" . date('Ymd') . ".xlsx";
        else $name = "Kurang-" . date('Ymd') . ".xlsx";

        return Excel::download(new InStockExportExcel($this->dataCetak), $name, \Maatwebsite\Excel\Excel::XLSX);
    }
}
