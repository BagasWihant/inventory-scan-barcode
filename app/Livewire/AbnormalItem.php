<?php

namespace App\Livewire;

use App\Models\itemIn;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Exports\InStockExport;
use Illuminate\Support\Facades\DB;
use App\Exports\InStockExportExcel;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReceivingSupplierReport;
use App\Models\PaletRegister;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;

class AbnormalItem extends Component
{
    use WithPagination;
    public $dataCetak, $searchKey, $status = '-', $userid, $isAdmin, $location = '-';
    public $dateFilter;

    public function __construct()
    {
        $user = auth()->user();
        $this->userid = $user->id;
        $this->isAdmin = $user->Admin;
    }
    public function render()
    {
        $query = DB::table('abnormal_materials')
            ->selectRaw('pallet_no,
                        material_no,
                        sum(picking_qty) as qty,
                        count(pallet_no) as pax,
                        trucking_id,
                        locate,status,
                        kit_no,line_c,
                        max(created_at) as created_at')
            ->when($this->isAdmin == 0, function ($query) {
                $query->where('user_id', $this->userid);
            })
            ->when($this->status, function ($query) {
                if ($this->status != '-') $query->where('status', $this->status);
                else $query->whereIn('status', ['0', '1']);
            })
            ->when($this->location != '-', function ($query) {
                if($this->location != 'other') $query->where('locate', $this->location);
                else $query->whereNotIn('locate', ['ASSY','CNC']);
            })
            ->when($this->searchKey, function ($query) {
                $query->where('pallet_no', 'like', "%$this->searchKey%")
                    ->orWhere('material_no', 'like', "%$this->searchKey%");
            })
            ->when($this->dateFilter, function ($query) {
                $query->where(DB::raw("FORMAT([created_at],'yyyy-MM-dd')"), 'like', "%$this->dateFilter%");
            })
            ->groupBy(['material_no', 'pallet_no', 'trucking_id', 'locate', 'status', 'kit_no', 'line_c']);

        $data = $query->paginate(20);
        if ($this->searchKey) $this->dispatch('searchFocus');

        $this->dataCetak = $data->items();

        return view('livewire.abnormal-item', compact('data'));
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
                'date' => date('Y-m-d')
            ];
            $this->dispatch('modalConfirm', $res);
        } else {
            // test sementara cek pakai huruf saja. jika masih ada bug kedepan, tambah cek ke tabel setupcnc
            $cekHurufDepan = ['Y-', 'C-'];
            if (in_array(substr($data->pallet_no, 0, 2), $cekHurufDepan)) {
                $res = [
                    'pallet_no' => $data->pallet_no,
                    'material_no' => $data->material_no,
                    'qty' => $data->qty,
                    'pax' => $data->pax,
                    'locate' => $data->locate,
                    'line' => $data->line_c,
                    'date' => date('Y-m-d')
                ];
                $this->dispatch('palletInput', $res);
            } else {

                $res = [
                    'pallet_no' => $data->pallet_no,
                    'material_no' => $data->material_no,
                    'qty' => $data->qty,
                    'pax' => $data->pax,
                    'lineC' => '-'
                ];
                $this->savingToStock($res);
            }
        }
    }

    #[On('kembalikan')]
    public function kembalikan($req)
    {
        $split = explode("|", $req);

        DB::table('abnormal_materials')
            ->where('pallet_no', $split[0])
            ->where('material_no', $split[1])
            ->where('user_id', $this->userid)
            ->update([
                'status' => '8'
            ]);

        return $this->dispatch('notif', [
            'icon' => 'success',
            'title' => 'Deleted from Abnormal Material',
        ]);
    }

    public function copyOldDataAndUpdate($data, $newPaletNo): object
    {
        $replaceData = [];
        $sumQty = 0;
        foreach ($data as $d) {
            $dataMentah = DB::table('abnormal_materials')->where('id', $d->id)->first();
            $replaceData = $dataMentah;

            $sumQty += $dataMentah->picking_qty;

            $loopData = (array) $dataMentah;
            $loopData['pallet_no'] = $newPaletNo . '~' . $loopData['pallet_no'];
            $loopData['status'] = '2';

            unset($loopData['id']);

            DB::table('abnormal_materials')->insert($loopData);
            DB::table('abnormal_materials')->where('id', $d->id)->update(['pallet_no' => $newPaletNo]);
        }
        $replaceData->pallet_no = $newPaletNo;
        $replaceData->counter = $sumQty;
        return $replaceData;
    }
    #[On('savingToStock')]
    public function savingToStock($req)
    {
        $dataDetail = DB::table('abnormal_materials as a')
            ->leftJoin('material_mst as b', 'a.material_no', '=', 'b.matl_no')

            ->select('a.id', 'pallet_no', 'material_no as material', 'b.matl_nm', 'picking_qty as counter', 'locate', 'trucking_id', 'line_c', 'setup_by', 'surat_jalan', 'kit_no')
            ->where('pallet_no', $req['pallet_no'])
            ->when($this->isAdmin == 0, function ($query) {
                $query->where('user_id', $this->userid);
            })
            ->where('material_no', $req['material_no']);
        try {
            $detail = $dataDetail->get();
            // untuk copy old data sebelum di ganti
            if (isset($req['palletNo_new'])) {
                $req['pallet_no'] = $req['palletNo_new'];
                $data = $this->copyOldDataAndUpdate($detail, $req['palletNo_new']);
            }

            itemIn::create([
                'pallet_no' => $data->pallet_no . ($data->locate == 'ASSY' ? '-AM' : ''),
                'material_no' => $data->material_no,
                'picking_qty' => $data->counter,
                'locate' => $data->locate,
                'trucking_id' => $data->trucking_id,
                'user_id' => $this->userid,
                'line_c' => $req['lineC'] != '-' ? $req['lineC'] : $data->line_c,
                'setup_by' => $data->setup_by,
                'surat_jalan' => $data->surat_jalan,
                'kit_no' => $data->kit_no
            ]);
            // foreach ($detail as $data) {
            // itemIn::create([
            //     'pallet_no' => $data->pallet_no . ($data->locate == 'ASSY' ? '-AM' : ''),
            //     'material_no' => $data->material,
            //     'picking_qty' => $data->counter,
            //     'locate' => $data->locate,
            //     'trucking_id' => $data->trucking_id,
            //     'user_id' => $this->userid,
            //     'line_c' => $req['lineC'] != '-' ? $req['lineC'] : $data->line_c,
            //     'setup_by' => $data->setup_by,
            //     'surat_jalan' => $data->surat_jalan,
            //     'kit_no' => $data->kit_no
            // ]);
            // }

            // status 9 untuk sudah konfirmasi
            DB::table('abnormal_materials')
                ->where('pallet_no', $req['pallet_no'])
                ->where('material_no', $req['material_no'])
                ->where('user_id', $this->userid)
                ->update([
                    'status' => '9'
                ]);

            $this->dispatch('notif', [
                'icon' => 'success',
                'title' => 'Success save to stock',
            ]);
            $dataPaletRegister = PaletRegister::selectRaw('palet_no,issue_date,line_c')->where('is_done', 1)->where('palet_no_iwpi', $data->pallet_no . ($data->locate == 'ASSY' ? '-AM' : ''))->latest()->first();

            if ($dataPaletRegister) {
                if ($req['lineC'] != '-') {
                    $dataPaletRegister->issue_date = $req['issue_date'];
                    $dataPaletRegister->save();
                }

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
        } catch (\Throwable $th) {
            $this->dispatch('notif', [
                'icon' => 'error',
                'title' => $th->getMessage(),
            ]);
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
