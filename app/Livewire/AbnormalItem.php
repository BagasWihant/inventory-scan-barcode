<?php

namespace App\Livewire;

use App\Models\itemIn;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Exports\InStockExport;
use Illuminate\Support\Facades\DB;
use App\Exports\InStockExportExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReceivingSupplierReport;
use App\Models\PaletRegister;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;

class AbnormalItem extends Component
{
    public $dataCetak, $searchKey, $status = '-', $userid, $isAdmin, $location = '-';

    public function __construct()
    {
        $user = auth()->user();
        $this->userid = $user->id;
        $this->isAdmin = $user->Admin;
    }
    public function render()
    {
        $query = DB::table('abnormal_materials')
            ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax,trucking_id,locate,status,kit_no,line_c')
            ->when($this->isAdmin == 0, function ($query) {
                $query->where('user_id', $this->userid);
            })
            ->when($this->status != '-', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->location != '-', function ($query) {
                $query->where('locate', $this->location);
            })
            ->groupBy(['material_no', 'pallet_no', 'trucking_id', 'locate', 'status', 'kit_no', 'line_c']);

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

    public function statusChange() {}
    public function locationChange() {}

    public function konfirmasi($id)
    {
        $split = explode("|", $id);
        $dataDetail = DB::table('abnormal_materials')
            ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax,locate,line_c')
            ->groupBy(['material_no', 'pallet_no', 'locate', 'line_c'])
            ->where('pallet_no', $split[0])
            ->where('material_no', $split[1]);
        // dump($dataDetail->first());
        $data = $dataDetail->first();
        if ($data->locate == 'ASSY') {
            $res = [
                'pallet_no' => $data->pallet_no,
                'material_no' => $data->material_no,
                'qty' => $data->qty,
                'pax' => $data->pax,
                'locate' => $data->locate,
                'line' => $data->line_c,
            ];
        } else {
            $res = [
                'pallet_no' => $data->pallet_no,
                'material_no' => $data->material_no,
                'qty' => $data->qty,
                'pax' => $data->pax,
            ];
        }
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
        $dataDetail = DB::table('abnormal_materials as a')
            ->leftJoin('material_mst as b', 'a.material_no', '=', 'b.matl_no')

            ->select('pallet_no', 'material_no as material', 'b.matl_nm', 'picking_qty as counter', 'locate', 'trucking_id', 'line_c', 'setup_by', 'surat_jalan', 'kit_no')
            ->where('pallet_no', $req['pallet_no'])
            ->when($this->isAdmin == 0, function ($query) {
                $query->where('user_id', $this->userid);
            })
            ->where('material_no', $req['material_no']);

        $detail = $dataDetail->get();
        foreach ($detail as $data) {
            $data->counter  = (int) $data->counter;
            itemIn::create([
                'pallet_no' => $data->pallet_no,
                'material_no' => $data->material,
                'picking_qty' => $data->counter,
                'locate' => $data->locate,
                'trucking_id' => $data->trucking_id,
                'user_id' => $this->userid,
                'line_c' => $req['lineC'] != '-' ? $req['lineC'] : $data->line_c,
                'setup_by' => $data->setup_by,
                'surat_jalan' => $data->surat_jalan,
                'kit_no' => $data->kit_no
            ]);
        }
        dump($req['lineC'] ,$data->line_c);

        DB::table('abnormal_materials')
            ->where('pallet_no', $req['pallet_no'])
            ->where('material_no', $req['material_no'])
            ->where('user_id', $this->userid)
            ->delete();

        $this->dispatch('notif', [
            'icon' => 'success',
            'title' => 'Success save to stock',
        ]);
        $dataPaletRegister = PaletRegister::selectRaw('palet_no,issue_date,line_c')->where('is_done', 1)->where('palet_no_iwpi', $data->pallet_no)->latest()->first();
        
        if ($dataPaletRegister) {
            $dataPaletRegister->issue_date = date('Y-m-d');
            $dataPaletRegister->save();

            $generator = new BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode($dataPaletRegister->palet_no, $generator::TYPE_CODE_128);
            Storage::put('public/barcodes/' . $dataPaletRegister->palet_no . '.png', $barcode);
            $dataPrint = [
                'data' => $detail,
                'palet_no' => $dataPaletRegister->palet_no,
                'issue_date' => $dataPaletRegister->issue_date,
                'line_c' => $dataPaletRegister->line_c,
                'abnormal' => true,
            ];
            return Excel::download(new ReceivingSupplierReport($dataPrint), "Receiving kelebihan ASSY_" . $dataPrint['palet_no'] . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
        }
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
