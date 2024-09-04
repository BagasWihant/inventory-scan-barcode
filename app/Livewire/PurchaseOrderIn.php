<?php

namespace App\Livewire;

use App\Exports\ReceivingSupplierNotAssyReport;
use App\Exports\ReceivingSupplierReport;
use App\Models\itemIn;
use Livewire\Component;
use App\Models\tempCounter;
use Livewire\Attributes\On;
use App\Models\abnormalMaterial;
use App\Models\PaletRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorPNG;
use stdClass;

class PurchaseOrderIn extends Component
{
    use WithPagination;
    public $userId, $po,  $listMaterialScan, $listKitNo = [], $sws_code, $statusLoading;
    public $searchPo;
    public $paletCode, $palet, $noPalet;
    public $surat_jalan;
    public $material_no;
    public $input_setup_by;
    public $suratJalanDisable = false, $paletDisable = false, $poDisable = false;
    public $lineCodeDisable = false, $lokasiDisable = false;
    public $getall = [];
    public $lokasi,$line_code_list=[], $line_code;

    public function mount()
    {
        $this->userId = auth()->user()->id;
    }
    public function updated($n, $v)
    {
        if ($n === 'po') {
            $this->suratJalanDisable = true;
            $this->paletDisable = true;
            $this->poDisable = true;
        }
    }

    public function updating($prop, $v)
    {
        switch ($prop) {
            case 'lokasi':
                $this->lokasiDisable = true;
                break;
            case 'line_code':
                $this->lineCodeDisable = true;
                break;

            default:
                # code...
                break;
        }
    }

    public function poChange()
    {
        if (strlen($this->searchPo) >= 3) {
            $this->listKitNo = DB::table('material_setup_mst_supplier')
                ->selectRaw('kit_no')
                ->where('kit_no', 'like', "%$this->searchPo%")
                ->groupBy('kit_no')
                ->limit(10)
                ->get();
        }
    }
    public function choosePo($po = null)
    {
        if ($this->surat_jalan !== null) {
            DB::table('temp_counters')->where('userID', $this->userId)->where('flag', 1)->delete();

            $getSetupby = DB::table('material_setup_mst_supplier')->select('setup_by')->where('kit_no', $po)->first();
            if ($getSetupby) $this->input_setup_by = $getSetupby->setup_by;

            $this->po = $po;
            $this->searchPo = $po;
            $this->listKitNo = [];

            $this->suratJalanDisable = true;
            $this->paletDisable = true;
            $this->poDisable = true;


            $joinCondition = function ($join) {
                $join->on('a.material_no', '=', 'b.material_no')
                    ->on('a.kit_no', '=', 'b.kit_no')
                    ->where('b.pallet_no', $this->paletCode);
            };
            $groupByColumns = ['a.material_no', 'a.kit_no', 'a.line_c', 'a.setup_by', 'a.picking_qty', 'b.picking_qty', 'scanned_time'];

            if ($this->input_setup_by == "PO COT") {
                $joinCondition = function ($join) {
                    $join->on('a.material_no', '=', 'b.material_no')
                        ->on('a.kit_no', '=', 'b.kit_no')
                        ->on('a.line_c', '=', 'b.line_c')
                        ->where('b.pallet_no', $this->paletCode);
                };
            }

            $productsQuery = DB::table('material_setup_mst_supplier as a')
                ->where('a.kit_no', $this->po)
                ->selectRaw('a.material_no, a.picking_qty, count(a.picking_qty) as pax, a.kit_no, b.picking_qty as stock_in, a.line_c, a.setup_by')
                ->leftJoin('material_in_stock as b', $joinCondition)
                ->groupBy($groupByColumns)
                ->orderByDesc('scanned_time')
                ->orderBy('a.material_no');
                $this->line_code_list = $productsQuery->pluck('line_c')->all();
            $getall = $productsQuery->get();
            foreach ($getall as $value) {
                try {
                    $total = $value->stock_in > 0 ? $value->picking_qty - $value->stock_in : $value->picking_qty;
                    DB::beginTransaction();
                    $insert = [
                        'material' => $value->material_no,
                        'palet' => $this->po,
                        'userID' => $this->userId,
                        'sisa' => $total,
                        'total' => $total,
                        'pax' => $value->pax,
                        'flag' => 1,
                        'prop_ori' => json_encode(['setup_by' => $value->setup_by]),
                        'line_c' => $value->line_c,
                    ];


                    tempCounter::create($insert);
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                }
            }

            return $this->dispatch('materialFocus');
        } else {
            return $this->dispatch('alert', ['title' => 'Warning', 'time' => 3500, 'icon' => 'warning', 'text' => 'Please fill all input to avoid inaccurate data']);
        }
    }
    public function materialNoScan()
    {
        // parsing  PCL-L24-1077 / S04803815100101502108P6001  / YD30  / 210824 / 2 / ANDIK
        // parsing  PCL-L24-1077 / S0360381510010160999999999  / YD30 / 210824 / 2 / ANDIK
        $qr = $this->material_no;
        $split = explode("/", $this->material_no);
        if (count($split) < 2) {
            $this->material_no = null;
            return $this->dispatch('alert', ['title' => 'Warning', 'time' => 3500, 'icon' => 'warning', 'text' => 'Material number not found']);
        }
        $this->insertNew($this->material_no,true);
        // $split1 = trim(str_replace(" ","",$split[1]));
        // $qtyParse = substr($split1, 1, 4);
        
        // $hapus9huruf = substr($split1, -9);
        // $hapusdepan = substr($split1, 0, 5);
        // $parse1 = str_replace($hapusdepan, "", $split1);
        // $material_noParse = str_replace($hapus9huruf, "", $parse1);

        // $lineParse = trim($split[2]);


        // $supplierCode = DB::table('material_conversion_mst')->where('supplier_code', $material_noParse)->select('sws_code')->first();
        // if ($supplierCode) {
        //     $this->sws_code = $supplierCode->sws_code;
        //     // $getTempCounterData = DB::table('temp_counters')->where('palet', $this->po)->where('material', $this->material_no);


        //     $dataInsert = [
        //         'qty' => $qtyParse,
        //         'lineNew' => $lineParse,
        //         'location' => 'ASSY',
        //         'qr' => $qr
        //     ];
        //     $this->insertNew($dataInsert, true);
        // }
        // $this->material_no = null;
    }

    #[On('insertNew')]
    public function insertNew($reqData = null, $update = false)
    {
        $insert = DB::select('EXEC sp_WH_rcv_QRConvert2 ?,?', [$reqData, $this->userId]);
        if ($insert[0]->status !== '1') {
            $this->material_no = null;
            return $this->dispatch('alert', ['title' => 'Warning', 'time' => 4000, 'icon' => 'warning', 'text' => $insert[0]->status]);
        }

        if ($update && $reqData !== null) {
            $tempCount = DB::table('temp_counters')
                ->where('material', $this->sws_code)
                ->where('userID', $this->userId)
                ->where('palet', $this->po);
            if (isset($reqData['lineNew']) && $reqData['lineNew'] !== "") {

                $tempCount->where('line_c', $reqData['lineNew']);
            }
            if (!$tempCount->exists()) {
                $this->material_no = null;
                DB::table('WH_rcv_QRHistory')->where('QR', $reqData['qr'])->delete();
                return $this->dispatch('alert', ['title' => 'Warning', 'time' => 4000, 'icon' => 'warning', 'text' => "Material Tidak ada"]);
            }
            $data = $tempCount->first();
            $counter = $data->counter + $reqData['qty'];


            $sisa = $data->sisa - $reqData['qty'];
            // save location on modal to prop_ori
            $prop_ori_update = json_decode($data->prop_ori, true);
            $prop_ori_update['location'] = $reqData['location'];

            $updatePropOnly = [
                'prop_ori' => json_encode($prop_ori_update)
            ];
            // TIDAK BISA BUAT temp variable
            DB::table('temp_counters')
                ->where('material', $this->sws_code)
                ->where('userID', $this->userId)
                ->where('palet', $this->po)->update($updatePropOnly);

            $new_prop_scan = isset($data->prop_scan) ? json_decode($data->prop_scan) : [];
            array_push($new_prop_scan, $reqData['qty']);

            // update scanned time material setup
            $scannedTime = now();
            DB::table('material_setup_mst_supplier')->where('kit_no', $this->po)->where('material_no', $this->sws_code)
                ->when(!empty($reqData['lineNew']), fn($q) => $q->where('line_c', $reqData['lineNew']))
                ->update([
                    'scanned_time' => $scannedTime,
                    'being_used' => $this->userId
                ]);

            if ($data->total < $data->counter || $data->sisa <= 0) {
                // kelebihan
                $this->material_no = null;
                $more = $data->qty_more + 1;
                if (isset($reqData['lineNew']) && $reqData['lineNew'] !== "") {
                    $updateData = [
                        'line_c' => $reqData['lineNew'],
                        'counter' => $counter,
                        'sisa' => $sisa,
                        'qty_more' => $more,
                        'prop_scan' => json_encode($new_prop_scan),
                        'scanned_time' => $scannedTime,
                    ];
                } else {
                    $updateData = [
                        'counter' => $counter,
                        'sisa' => $sisa,
                        'qty_more' => $more,
                        'prop_scan' => json_encode($new_prop_scan),
                        'scanned_time' => $scannedTime,
                    ];
                }
                $tempCount->update($updateData);
                return;
            } else {
                if (isset($reqData['lineNew']) && $reqData['lineNew'] !== "") {
                    $updateData = [
                        'counter' => $counter,
                        'sisa' => $sisa,
                        'prop_scan' => json_encode($new_prop_scan),
                        'line_c' => $reqData['lineNew'],
                        'scanned_time' => $scannedTime,
                    ];
                } else {
                    $updateData = [
                        'counter' => $counter,
                        'sisa' => $sisa,
                        'prop_scan' => json_encode($new_prop_scan),
                        'scanned_time' => $scannedTime,
                    ];
                }

                $tempCount->update($updateData);
            }
        }
        $this->material_no = null;
    }

    public function resetItem($req)
    {
        $qryUPdate = tempCounter::where('palet', $req[1])->where('material', $req[0])->where('line_c', $req[3]);
        $data = $qryUPdate->first();
        $decodePropOri = json_decode($data->prop_ori, true);
        $tmp['setup_by'] = $decodePropOri['setup_by'];
        $newPropOri = json_encode($tmp);
        if (isset($req[2]) && $req[2] == 'PO MCS') {
            $dataUpdate =  [
                'sisa' => $data->total,
                'counter' => 0,
                'qty_more' => 0,
                'prop_scan' => null,
                'line_c' => null
            ];
        } else {
            $dataUpdate =  [
                'sisa' => $data->total,
                'counter' => 0,
                'qty_more' => 0,
                'prop_scan' => null,
                'prop_ori' => $newPropOri
            ];
        }
        $qryUPdate->update($dataUpdate);
        $this->dispatch('SJFocus');
    }
    public function confirm()
    {

        $fixProduct = DB::table('temp_counters')
            ->leftJoin('delivery_mst as d', 'temp_counters.palet', '=', 'd.pallet_no')
            // ->leftJoin('matloc_temp_CNCKIAS2 as m', 'temp_counters.material', '=', 'm.material_no')
            ->leftJoin('material_mst as b', 'temp_counters.material', '=', 'b.matl_no')

            ->select('temp_counters.*', 'd.trucking_id', 'b.loc_cd as location_cd', 'matl_nm')
            ->where('userID', $this->userId)
            ->where('flag', 1)
            ->where('palet', $this->po)
            ->orderByDesc('scanned_time');

        // remove data in abnormal_materials
        $dd = abnormalMaterial::where(['pallet_no' => $this->paletCode, 'kit_no' => $this->po,])->delete();

        $loopData = $fixProduct->get();
        $collectionTempCounter = collect($loopData);
        $checkASSY = tempCounter::where('userID', $this->userId)
            ->where('flag', 1)
            ->where('palet', $this->po)->where('prop_ori', 'like', '%"location":"ASSY"%')->exists();
        $checkNotASSY = tempCounter::where('userID', $this->userId)
            ->where('flag', 1)->whereNotNull('prop_scan')
            ->where('palet', $this->po)->where('prop_ori', 'not like', '%"location":"ASSY"%')->exists();

        if ($checkASSY && $checkNotASSY) {
            return $this->dispatch('alert', ['title' => 'Warning', 'time' => 5000, 'icon' => 'error', 'text' => 'Double location detected ASSY and CNC']);
        }

        foreach ($loopData as $data) {
            $pax = $data->pax;
            $qty = $data->total / $pax;
            $kelebihan = $data->qty_more;
            $prop_ori = json_decode($data->prop_ori, true);

            if (!isset($prop_ori['setup_by'])) {
                $prop_ori['setup_by'] = null;
            }

            if ($data->prop_scan != null) {

                $prop_scan = json_decode($data->prop_scan, true);
                $masuk = 1;
                foreach ($prop_scan as $value) {
                    if ($masuk <= $data->pax || $data->total > $data->counter) {
                        itemIn::create([
                            'pallet_no' => $this->paletCode,
                            'material_no' => $data->material,
                            'picking_qty' => $value,
                            'locate' => $data->location_cd,
                            'trucking_id' => $data->trucking_id,
                            'kit_no' => $this->po,
                            'surat_jalan' => $this->surat_jalan,
                            'user_id' => $this->userId,
                            'line_c' => $data->line_c,
                            'locate' => $prop_ori['location'] ?? null,
                            'setup_by' => $prop_ori['setup_by'],
                        ]);
                    } else {
                        // abnormalMaterial::create([
                        //     'kit_no' => $this->po,
                        //     'surat_jalan' => $this->surat_jalan,
                        //     'pallet_no' => $this->paletCode,
                        //     'material_no' => $data->material,
                        //     'picking_qty' => $value,
                        //     'locate' => $data->location_cd,
                        //     'trucking_id' => $data->trucking_id,
                        //     'user_id' => $this->userId,
                        //     'status' => 1,
                        //     'line_c' => $data->line_c,
                        //     'locate' => $prop_ori['location'] ?? null,
                        //     'setup_by' => $prop_ori['setup_by'],
                        // ]);
                    }
                    $masuk++;
                }
                // kurang
                if ($data->total > $data->counter) {
                    $count = $data->pax - $masuk;
                    $kurangnya = $data->total - $data->counter;
                    // abnormalMaterial::create([
                    //     'pallet_no' => $this->paletCode,
                    //     'kit_no' => $this->po,
                    //     'surat_jalan' => $this->surat_jalan,
                    //     'material_no' => $data->material,
                    //     'picking_qty' => $kurangnya,
                    //     'locate' => $data->location_cd,
                    //     'trucking_id' => $data->trucking_id,
                    //     'user_id' => $this->userId,
                    //     'status' => 0,
                    //     'line_c' => $data->line_c,
                    //     'locate' => $prop_ori['location'] ?? null,
                    //     'setup_by' => $prop_ori['setup_by'],
                    // ]);
                }
            } else {
                $sisa = $data->sisa;
                for ($i = 1; $i <= $data->pax; $i++) {
                    $qty = floor($data->sisa / $data->pax);
                    $sisa = $sisa - $qty;
                    if ($i > $data->pax) $qty = $sisa;
                    // abnormalMaterial::create([
                    //     'kit_no' => $this->po,
                    //     'surat_jalan' => $this->surat_jalan,
                    //     'pallet_no' => $this->paletCode,
                    //     'material_no' => $data->material,
                    //     'picking_qty' => $qty,
                    //     'locate' => $data->location_cd,
                    //     'trucking_id' => $data->trucking_id,
                    //     'user_id' => $this->userId,
                    //     'status' => 0,
                    //     'line_c' => $data->line_c,
                    //     'locate' => $prop_ori['location'] ?? null,
                    //     'setup_by' => $prop_ori['setup_by'],
                    // ]);
                }
            }
        }

        // JIKA ASSY
        if ($checkASSY) {
            $dataPaletRegister = PaletRegister::selectRaw('palet_no,issue_date,line_c')->where('is_done', 1)->where('palet_no_iwpi', $this->paletCode)->first();

            // insert
            DB::table('WH_rcv_QRHistory')->where('user_id', $this->userId)->update([
                'status' => 1,
                'PO' => $this->po,
                'surat_jalan' => $this->surat_jalan,
                'palet_iwpi' => $this->paletCode,
            ]);
            if ($dataPaletRegister) {
                $generator = new BarcodeGeneratorPNG();
                $barcode = $generator->getBarcode($dataPaletRegister->palet_no, $generator::TYPE_CODE_128);
                Storage::put('public/barcodes/' . $dataPaletRegister->palet_no . '.png', $barcode);

                $dataPrint = [
                    'data' => $loopData,
                    'palet_no' => $dataPaletRegister->palet_no,
                    'issue_date' => $dataPaletRegister->issue_date,
                    'line_c' => $dataPaletRegister->line_c
                ];
                $this->resetPage();
                $this->dispatch('alert', ['title' => 'Succes', 'time' => 5000, 'icon' => 'succes', 'text' => 'ASSY material saved succesfully']);
                return Excel::download(new ReceivingSupplierReport($dataPrint), "Receiving ASSY_" . $dataPrint['palet_no'] . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
            } else {
                $this->resetPage();
                return $this->dispatch('alert', ['title' => 'Succes', 'time' => 5000, 'icon' => 'succes', 'text' => 'material saved succesfully without palet']);
            }
        }
        $dataPrint = [
            'data' => $loopData,
            'palet_no' => $this->paletCode,
        ];
        $this->resetPage();
        $this->dispatch('alert', ['time' => 5000, 'icon' => 'success', 'title' => 'Other ASSY material saved succesfully']);
        return Excel::download(new ReceivingSupplierNotAssyReport($dataPrint), "Receiving Not ASSY_" . $dataPrint['palet_no'] . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }

    public function resetPage()
    {

        // reset scanned time in material supplier
        DB::table('material_setup_mst_supplier')->where('kit_no', $this->po)->where('being_used', 1)
            ->update([
                'scanned_time' => null,
                'being_used' => null,
            ]);

        $this->input_setup_by = null;
        $this->lokasiDisable = false;
        $this->suratJalanDisable = false;
        $this->paletDisable = false;
        $this->poDisable = false;
        $this->po = null;
        $this->surat_jalan = null;
        $this->palet = null;
        $this->searchPo = null;
        $this->noPalet = null;
        $this->paletCode = null;
        // $this->listMaterial = [];
        $this->listMaterialScan = [];
        DB::table('temp_counters')->where('userID', $this->userId)->where('flag', 1)->delete();
        $this->dispatch('SJFocus');
    }

    public function render()
    {
        $this->paletCode = $this->palet . "-" . $this->noPalet;


        $joinCondition = function ($join) {
            $join->on('a.material_no', '=', 'b.material_no')
                ->on('a.kit_no', '=', 'b.kit_no')
                ->where('b.pallet_no', $this->paletCode);
        };
        $groupByColumns = ['a.material_no', 'a.kit_no', 'a.line_c', 'a.setup_by', 'a.picking_qty', 'b.picking_qty', 'scanned_time'];

        if ($this->input_setup_by == "PO COT") {
            $joinCondition = function ($join) {
                $join->on('a.material_no', '=', 'b.material_no')
                    ->on('a.kit_no', '=', 'b.kit_no')
                    ->on('a.line_c', '=', 'b.line_c')
                    ->where('b.pallet_no', $this->paletCode);
            };
        }




        $tempQuery = DB::table('temp_counters as a')
            ->leftJoin('material_mst as b', 'a.material', '=', 'b.matl_no')
            ->where('palet', $this->po)
            ->where('counter', '>', 0)
            ->select('a.*', 'b.loc_cd as location_cd')
            ->where('userID', $this->userId)
            ->orderByDesc('scanned_time')
            ->orderBy('material');
        $material_no_list = $tempQuery->pluck('material')->all();

        $productsQuery = DB::table('material_setup_mst_supplier as a')
            ->whereIn('a.material_no', $material_no_list)
            ->where('a.kit_no', $this->po)
            ->selectRaw('a.material_no, a.picking_qty, count(a.picking_qty) as pax, a.kit_no, b.picking_qty as stock_in, a.line_c, a.setup_by')
            ->leftJoin('material_in_stock as b', $joinCondition)
            ->groupBy($groupByColumns)
            ->orderByDesc('scanned_time')
            ->orderBy('a.material_no');


        $listMaterial = $productsQuery->paginate(20);
        $sudahScan = $tempQuery->paginate(20);
        // dump($scannedCounter)


        $this->dispatch('paletFocus');


        return view(
            'livewire.purchase-order-in',
            [
                'sudahScan' => $sudahScan,
                'listMaterial' => $listMaterial,
            ]
        );
    }
}
