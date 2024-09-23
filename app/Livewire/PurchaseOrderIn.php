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
    public $lokasiDisable = false, $mcs = false;
    public $getall = [];
    public $lokasi, $reset = false, $line_code;

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
                $this->dispatch('materialFocus');
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

            $this->reset = true;
            $this->suratJalanDisable = true;
            $this->paletDisable = true;
            $this->poDisable = true;

            $joinCondition = function ($join) {
                $join->on('a.material_no', '=', 'b.material_no')
                    ->on('a.kit_no', '=', 'b.kit_no');
                    // ->where('b.pallet_no', $this->paletCode);
            };
            $groupByColumns = ['a.material_no', 'a.kit_no', 'a.line_c', 'a.setup_by', 'a.picking_qty',  'scanned_time'];

            if ($this->input_setup_by == "PO COT") {
                $this->mcs = true;
                $joinCondition = function ($join) {
                    $join->on('a.material_no', '=', 'b.material_no')
                        ->on('a.kit_no', '=', 'b.kit_no')
                        ->on('a.line_c', '=', 'b.line_c');
                        // ->where('b.pallet_no', $this->paletCode);
                };
            }

            $productsQuery = DB::table('material_setup_mst_supplier as a')
                ->where('a.kit_no', $this->po)
                ->selectRaw('a.material_no, a.picking_qty, count(a.picking_qty) as pax, a.kit_no, sum(b.picking_qty) as stock_in, a.line_c, a.setup_by')
                ->leftJoin('material_in_stock as b', $joinCondition)
                ->groupBy($groupByColumns)
                ->orderByDesc('scanned_time')
                ->orderBy('a.material_no');
dump($productsQuery->toRawSql());
            $getall = $productsQuery->get();
            foreach ($getall as $value) {
                try {
                    // $total = $value->stock_in > 0 ? $value->picking_qty - $value->stock_in : $value->picking_qty;
                    $mroe = $value->stock_in > $value->picking_qty ? 1 : 0;
                    DB::beginTransaction();
                    $insert = [
                        'material' => $value->material_no,
                        'palet' => $this->po,
                        'userID' => $this->userId,
                        'sisa' => $value->picking_qty,
                        'total' => $value->picking_qty,
                        'pax' => $value->pax,
                        'flag' => 1,
                        'qty_more' => $mroe,
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
        $this->dispatch('playSound');
        if (!$this->lokasi && $this->input_setup_by == "PO COT") {
            return $this->dispatch('alert', ['title' => 'Warning', 'time' => 3500, 'icon' => 'warning', 'text' => 'Please choose Location']);
        }

        $qr = $this->material_no;
        $qrtrim = trim(str_replace(" ", "", $this->material_no));
        if (strpos($qrtrim, "//")) {
            // MCS
            $split = explode("//", $qrtrim);
            if (count($split) < 2) {
                $this->material_no = null;
                return;
                // $this->dispatch('alert', ['title' => 'Warning', 'time' => 3500, 'icon' => 'warning', 'text' => 'QR Code not valid']);
            }
            $split1 = explode("-", $split[0]);

            $material_noParse = $split1[0];
            $qtyParse = preg_replace('/[^0-9]/', '', $split1[2]);
            $lineParse = "";
        } else {
            $split = explode("/", $qrtrim);
            if (count($split) < 2) {
                $this->material_no = null;
                return;
                // $this->dispatch('alert', ['title' => 'Warning', 'time' => 3500, 'icon' => 'warning', 'text' => 'Material number not found']);
            }
            $qtyParse = substr($split[1], 1, 4);

            $hrfBkg = substr($split[1], -9);
            $hrfDpn = substr($split[1], 0, 5);

            $hapusdepan = str_replace($hrfDpn, "", $split[1]);
            $material_noParse = str_replace($hrfBkg, "", $hapusdepan);

            $lineParse = trim($split[2]);

            if ($this->line_code != null && $this->line_code != $lineParse) {
                $this->material_no = null;
                return $this->dispatch('alert', ['title' => 'Warning', 'time' => 3500, 'icon' => 'warning', 'text' => 'PO atau Linecode berbbeda']);
            }
        }


        $supplierCode = DB::table('material_conversion_mst')->where('supplier_code', $material_noParse)->select('sws_code')->first();
        if ($supplierCode) {
            $this->sws_code = $supplierCode->sws_code;
            // $getTempCounterData = DB::table('temp_counters')->where('palet', $this->po)->where('material', $this->material_no);


            $dataInsert = [
                'qty' => $qtyParse,
                'lineNew' => $lineParse,
                'location' => $this->lokasi,
                'qr' => $qr
            ];
            $this->insertNew($dataInsert);
        }else{
            $this->dispatch('alert', ['title' => 'Warning', 'time' => 3500, 'icon' => 'warning', 'text' => DB::table('material_conversion_mst')->where('supplier_code', $material_noParse)->select('sws_code')->toRawSql()]);
        }
        $this->material_no = null;
    }

    #[On('insertNew')]
    public function insertNew($reqData = null, $update = false)
    {
        if ($reqData !== null) {
            $tempCount = DB::table('temp_counters')
                ->where('material', $this->sws_code)
                ->where('userID', $this->userId)
                ->where('palet', $this->po);
            if (isset($reqData['lineNew']) && $reqData['lineNew'] !== "") {
                $tempCount->where('line_c', $reqData['lineNew']);
            }

            if (!$tempCount->exists()) {
                $this->material_no = null;
                return $this->dispatch('alert', ['title' => 'Warning', 'time' => 400000, 'icon' => 'warning', 'text' => $tempCount->toRawSql()]);
            }

            if ($this->input_setup_by=="PO COT" && DB::table('WH_rcv_QRHistory')->where('QR', $reqData['qr'])->where('user_id', $this->userId)->where('status', 1)->exists()) {
                return $this->dispatch('alert', ['title' => 'Warning', 'time' => 4000, 'icon' => 'warning', 'text' => "QR sudah pernah discan"]);
            }

            // PCL-L24-1607 / S0200381510010150          2108P6002  / yd30  / 210824 / 2 / ANDIK
            $this->line_code = $reqData['lineNew'];

            DB::table('WH_rcv_QRHistory')->insert([
                'QR' => $reqData['qr'],
                'user_id' => $this->userId,
                'PO' => $this->po,
                'line_code' => $this->line_code,
                'material_no' => $this->sws_code,
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);

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
        $dataUpdate = [
            'sisa' => $data->total,
            'counter' => 0,
            'qty_more' => 0,
            'prop_scan' => null,
            'prop_ori' => isset($req[2]) && $req[2] == 'PO MCS' ? $data->prop_ori : $newPropOri,
        ];

        DB::table('WH_rcv_QRHistory')->where('material_no', $this->sws_code)
            ->where('PO', $this->po)
            ->where('user_id', $this->userId)->delete();

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
            ->where('temp_counters.line_c', $this->line_code)
            ->where('palet', $this->po)
            ->orderByDesc('scanned_time');

        // remove data in abnormal_materials
        $dd = abnormalMaterial::where(['pallet_no' => $this->paletCode, 'kit_no' => $this->po,])->delete();

        // GENERATE PALET CODE
        $getConfig = DB::table('WH_config')->select('value')->whereIn('config', ['PalletCodeInStock', 'PeriodInStock'])->get();
        $ym = date('ym');

        $PalletCodeInStock = (int)$getConfig[0]->value + 1;
        if ($getConfig[1]->value != $ym) {
            $PalletCodeInStock = 1;
            DB::table('WH_config')->where('config', 'PeriodInStock')->update(['value' => $ym]);
        }


        $generatePaletCode = str_pad($PalletCodeInStock, 4, '0', STR_PAD_LEFT);
        $this->paletCode = 'L-' . $ym . '-' . $generatePaletCode;

        DB::table('WH_config')->where('config', 'PalletCodeInStock')->update(['value' => $PalletCodeInStock]);

        $loopData = $fixProduct->get();
        $collectionTempCounter = collect($loopData);
        // $checkASSY = tempCounter::where('userID', $this->userId)
        //     ->where('flag', 1)
        //     ->where('palet', $this->po)->where('prop_ori', 'like', '%"location":"ASSY"%')->exists();
        // $checkNotASSY = tempCounter::where('userID', $this->userId)
        //     ->where('flag', 1)->whereNotNull('prop_scan')
        //     ->where('palet', $this->po)->where('prop_ori', 'not like', '%"location":"ASSY"%')->exists();

        // if ($checkASSY && $checkNotASSY) {
        //     return $this->dispatch('alert', ['title' => 'Warning', 'time' => 5000, 'icon' => 'error', 'text' => 'Double location detected ASSY and CNC']);
        // }

        foreach ($loopData as $data) {
            $pax = $data->pax;
            $qty = $data->total / $pax;
            // $kelebihan = $data->qty_more;
            $prop_ori = json_decode($data->prop_ori, true);

            if (!isset($prop_ori['setup_by'])) {
                $prop_ori['setup_by'] = null;
            }

            if ($data->prop_scan != null) {

                $prop_scan = json_decode($data->prop_scan, true);
                $totalScanPerMaterial = count($prop_scan);
                $iteration = 1;
                $kelebihan = $data->counter - abs($data->total);
                $sisaTerakhir = 0;
                foreach ($prop_scan as $value) {
                    if (($iteration == $totalScanPerMaterial) && ($kelebihan > 0 || $data->total < 0 ) ) {
                        $picking_qty = $data->total < 0 ? $value : $kelebihan;
                        abnormalMaterial::create([
                            'kit_no' => $this->po,
                            'surat_jalan' => $this->surat_jalan,
                            'pallet_no' => $this->paletCode,
                            'material_no' => $data->material,
                            'picking_qty' => $picking_qty,
                            'locate' => $data->location_cd,
                            'trucking_id' => $data->trucking_id,
                            'user_id' => $this->userId,
                            'status' => 1,
                            'line_c' => $data->line_c,
                            'locate' => $prop_ori['location'] ?? null,
                            'setup_by' => $prop_ori['setup_by'],
                        ]);
                        $sisaTerakhir = $value - $picking_qty;
                        if($sisaTerakhir > 0){
                            itemIn::create([
                                'pallet_no' => $this->paletCode,
                                'material_no' => $data->material,
                                'picking_qty' => $sisaTerakhir,
                                'locate' => $data->location_cd,
                                'trucking_id' => $data->trucking_id,
                                'kit_no' => $this->po,
                                'surat_jalan' => $this->surat_jalan,
                                'user_id' => $this->userId,
                                'line_c' => $data->line_c,
                                'locate' => $prop_ori['location'] ?? null,
                                'setup_by' => $prop_ori['setup_by'],
                            ]);
                        }
                    }else{
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
                    }
                    $iteration++;
                }
                // kurang
                if ($data->total > $data->counter) {
                    // $count = $data->pax - $masuk;
                    // $kurangnya = $data->total - $data->counter;
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

        DB::table('WH_rcv_QRHistory')
            ->where('user_id', $this->userId)
            ->where('palet_iwpi', null)
            ->when($this->input_setup_by == "PO COT" && $this->lokasi == 'ASSY', function ($q) {
                $q->where('line_code', $this->line_code);
            })
            ->where("PO", $this->po)->update([
                'status' => 1,
                'surat_jalan' => $this->surat_jalan,
                'palet_iwpi' => $this->paletCode,
            ]);
        // JIKA ASSY
        if ($this->lokasi == 'ASSY') {
            $dataPaletRegister = PaletRegister::selectRaw('palet_no,issue_date,line_c')->where('is_done', 1)->where('palet_no_iwpi', $this->paletCode)->first();

            // insert
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
                $this->dispatch('confirmation');

                // $this->dispatch('alert', ['title' => 'Succes', 'time' => 5000, 'icon' => 'succes', 'text' => 'ASSY material saved succesfully']);
                return Excel::download(new ReceivingSupplierReport($dataPrint), "Receiving ASSY_" . $dataPrint['palet_no'] . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
            } else {
                $this->dispatch('confirmation');
                // return $this->dispatch('alert', ['title' => 'Succes', 'time' => 5000, 'icon' => 'succes', 'text' => 'material saved succesfully without palet']);
                // $this->resetPage();
            }
        }

        $dataPrint = [
            'data' => $loopData,
            'palet_no' => $this->paletCode,
        ];

        // $this->resetPage();
        // $this->dispatch('alert', ['time' => 5000, 'icon' => 'success', 'title' => 'Other ASSY material saved succesfully']);
        $this->dispatch('confirmation');
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

        DB::table('temp_counters')->where('userID', $this->userId)->where('flag', 1)->where('palet', $this->po)->delete();
        $this->line_code = null;
        $this->lokasiDisable = false;
        $this->lokasi = null;
        $this->material_no = null;
        $this->reset = false;
        $this->mcs = false;
        $this->input_setup_by = null;
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
        $this->dispatch('SJFocus');
    }

    #[On('resetConfirm')]
    public function resetConfirm($type)
    {
        tempCounter::where('userID', $this->userId)
            ->where('palet', $this->po)->update(['prop_scan' => null]);
        if ($type == 0) {
            if ($this->input_setup_by == "PO MCS") {
                DB::table('temp_counters')->where('userID', $this->userId)->where('flag', 1)->delete();
                $this->choosePo($this->po);
            }
            $this->dispatch('materialFocus');
            $this->line_code = null;
            $this->material_no = null;
            $this->reset = false;
            $this->listMaterialScan = [];
        } else {
            $this->resetPage();
        }
    }

    public function render()
    {
        $this->paletCode = $this->palet . "-" . $this->noPalet;


        $joinCondition = function ($join) {
            $join->on('a.material_no', '=', 'b.material_no')
                ->on('a.kit_no', '=', 'b.kit_no');
                // ->where('b.pallet_no', $this->paletCode);
        };
        $groupByColumns = ['a.material_no', 'a.kit_no', 'a.line_c', 'a.setup_by', 'a.picking_qty',  'scanned_time'];

        if ($this->input_setup_by == "PO COT") {
            $joinCondition = function ($join) {
                $join->on('a.material_no', '=', 'b.material_no')
                    ->on('a.kit_no', '=', 'b.kit_no')
                    ->on('a.line_c', '=', 'b.line_c');
                    // ->where('b.pallet_no', $this->paletCode);
            };
        }




        $tempQuery = DB::table('temp_counters as a')
            ->leftJoin('material_mst as b', 'a.material', '=', 'b.matl_no')
            ->where('palet', $this->po)
            ->where('line_c', $this->line_code)
            ->select('a.*', 'b.loc_cd as location_cd')
            ->where('userID', $this->userId)
            ->orderByDesc('scanned_time')
            ->orderBy('material');
        $material_no_list = $tempQuery->pluck('material')->all();

        $productsQuery = DB::table('material_setup_mst_supplier as a')
            ->whereIn('a.material_no', $material_no_list)
            ->where('a.kit_no', $this->po)
            ->where('a.line_c', $this->line_code)

            ->selectRaw('a.material_no, a.picking_qty, count(a.picking_qty) as pax, a.kit_no, sum(b.picking_qty) as stock_in, a.line_c, a.setup_by')
            ->leftJoin('material_in_stock as b', $joinCondition)
            ->groupBy($groupByColumns)
            ->orderByDesc('scanned_time')
            ->orderBy('a.material_no');

            // dump($productsQuery->toRawSql());

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
