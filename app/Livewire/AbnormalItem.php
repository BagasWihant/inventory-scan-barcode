<?php

namespace App\Livewire;

use App\Models\itemIn;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Exports\InStockExport;
use Illuminate\Support\Facades\DB;
use App\Exports\InStockExportExcel;
use Maatwebsite\Excel\Facades\Excel;

class AbnormalItem extends Component
{
    public $dataCetak, $searchKey, $status='-',$userid,$isAdmin;

    public function __construct()
    {
        $user = auth()->user();
        $this->userid = $user->id;
        $this->isAdmin = $user->Admin;
    }
    public function render()
    {
        $query = DB::table('abnormal_materials')
            ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax,trucking_id,locate,status')
            ->when($this->isAdmin == 0, function ($query) {
                $query->where('user_id', $this->userid);
            })
            ->when($this->status != '-', function ($query) {
                $query->where('status', $this->status);
            })
            ->groupBy(['material_no', 'pallet_no', 'trucking_id', 'locate', 'status']);
            
        $query->where(function ($query) {
            $query->where('pallet_no', 'like', "%$this->searchKey%")->orWhere('material_no', 'like', "%$this->searchKey%");
        });

        $data = $query->get();
        $dev['a'] = $query->toRawSql();
        $dev['k'] = $this->status;

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
        $res = [
            'pallet_no' => $data->pallet_no,
            'material_no' => $data->material_no,
            'qty' => $data->qty,
            'pax' => $data->pax
        ];
        $this->dispatch('modalConfirm', $res);
    }

    #[On('kembalikan')]
    public function kembalikan($req)
    {
        $split = explode("|", $req);
                
        DB::table('abnormal_materials')
            ->where('pallet_no', $split[0])
            ->where('material_no', $split[1])
            ->where('user_id', $this->userid)
            ->delete();

        return $this->dispatch('notif', [
            'icon' => 'success',
            'title' => 'Deleted from Abnormal Material',
        ]);
    }

    #[On('savingToStock')]
    public function savingToStock($req)
    {
        $dataDetail = DB::table('abnormal_materials')
            ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax,locate,trucking_id,line_c,setup_by,surat_jalan,kit_no')
            ->groupBy(['material_no', 'pallet_no','locate','trucking_id','line_c','setup_by','surat_jalan','kit_no'])
            ->where('pallet_no', $req['pallet_no'])
            ->when($this->isAdmin == 0, function ($query) {
                $query->where('user_id', $this->userid);
            })
            ->where('material_no', $req['material_no']);
            dump($dataDetail->toRawSql());
        $data = $dataDetail->first();
        $perpax = $req['qty'] / $data->pax;
        $picking_qty = round($perpax);
        $total = $req['qty'];
        for ($i = 0; $i < $req['pax']; $i++) {
            if ($total > $picking_qty) {
                $total = $total - $picking_qty;
            }else{
                $picking_qty = $total;
            }
            itemIn::create([
                'pallet_no' => $data->pallet_no,
                'material_no' => $data->material_no,
                'picking_qty' => $picking_qty,
                'locate' => $data->locate,
                'trucking_id' => $data->trucking_id,
                'user_id' => $this->userid,
                'line_c'=> $data->line_c,
                'setup_by' => $data->setup_by,
                'surat_jalan' => $data->surat_jalan,
                'kit_no' => $data->kit_no
            ]);
        }

        DB::table('abnormal_materials')
            ->where('pallet_no', $req['pallet_no'])
            ->where('material_no', $req['material_no'])
            ->where('user_id', $this->userid)
            ->delete();
        
        return $this->dispatch('notif', [
            'icon' => 'success',
            'title' => 'Success save to stock',
        ]);

        // dump($data, $req);
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
