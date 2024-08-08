<?php

namespace App\Livewire;

use App\Models\itemIn;
use Livewire\Component;
use App\Models\tempCounter;
use Livewire\Attributes\On;
use App\Models\abnormalMaterial;
use Illuminate\Support\Facades\DB;

class PurchaseOrderIn extends Component
{
    public $userId, $po, $listMaterial = [], $listMaterialScan, $listKitNo = [], $sws_code, $statusLoading;
    public $searchPo;
    public $paletCode, $palet, $noPalet;
    public $surat_jalan;
    public $material_no;
    public $input_setup_by;
    public $suratJalanDisable = false, $paletDisable = false, $poDisable = false;

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
        if ($prop === 'po') {


            // DB::table('temp_counters')->where('userID', $this->userId)->where('flag', 1)->delete();
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
        if ($this->paletCode !== '-') {
            DB::table('temp_counters')->where('userID', $this->userId)->where('flag', 1)->delete();
            $this->po = $po;
            $this->searchPo = $po;
            $this->listKitNo = [];

            $this->suratJalanDisable = true;
            $this->paletDisable = true;
            $this->poDisable = true;
        } else {
            return $this->dispatch('alert', ['title' => 'Warning', 'time' => 3500, 'icon' => 'warning', 'text' => 'Please input Palet first, to avoid inaccurate data']);
        }
    }
    public function materialNoScan()
    {
        $supplierCode = DB::table('material_conversion_mst')->where('supplier_code', $this->material_no)->select('sws_code')->first();
        if ($supplierCode) {
            $this->sws_code = $supplierCode->sws_code;
            // $getTempCounterData = DB::table('temp_counters')->where('palet', $this->po)->where('material', $this->material_no);


            $mat_mst = DB::table('material_mst')
                ->select(['iss_min_lot', 'loc_cd'])
                ->where('matl_no', $this->sws_code)->first();
            $check_lineNsetup = DB::table('material_setup_mst_supplier')->select(['line_c', 'setup_by', 'picking_qty'])->where('kit_no', $this->po)->where('material_no', $this->sws_code)->get()->toArray();

            if ($mat_mst->iss_min_lot == 1) {
                $this->material_no = null;
                if ($check_lineNsetup) {
                    // check lokasi di kolom prop ori
                    $checkLocation = tempCounter::select('prop_ori')->where('palet', $this->po)->where('material', $this->sws_code)->first();
                    $decodePropOri = json_decode($checkLocation->prop_ori, true);
                    return $this->dispatch('newItem', [
                        'qty' => 0,
                        'title' => 'Material with manual Qty',
                        'update' => true,
                        'line' => $check_lineNsetup,
                        'loc_cd' => $mat_mst->loc_cd,
                        'locationSet' => isset($decodePropOri['location']) ? [$decodePropOri['location']] : null
                    ]);
                }
                return $this->dispatch('newItem', ['qty' => 0, 'title' => 'Material with manual Qty', 'update' => true]);
            } else {
                $data = ['qty' => $check_lineNsetup[0]->picking_qty, 'location' => $mat_mst->loc_cd];
                $this->insertNew($data,true);
            }
        }
        $this->material_no = null;
    }

    #[On('insertNew')]
    public function insertNew(array $reqData = null, $update = false)
    {
        if ($update && $reqData !== null) {
            $tempCount = DB::table('temp_counters')
                ->where('material', $this->sws_code)
                ->where('userID', $this->userId)
                ->where('palet', $this->po);
            if (isset($reqData['lineNew']) && $reqData['lineNew'] !== "" ) {

                $tempCount->where('line_c', $reqData['lineNew']);
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
                    ];
                } else {
                    $updateData = [
                        'counter' => $counter,
                        'sisa' => $sisa, 'qty_more' => $more,
                        'prop_scan' => json_encode($new_prop_scan),
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
                        'line_c' => $reqData['lineNew']
                    ];
                } else {
                    $updateData = [
                        'counter' => $counter,
                        'sisa' => $sisa,
                        'prop_scan' => json_encode($new_prop_scan)
                    ];
                }
                // dd($updateData);
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
            ->leftJoin('matloc_temp_CNCKIAS2 as m', 'temp_counters.material', '=', 'm.material_no')
            ->select('temp_counters.*', 'd.trucking_id', 'm.location_cd')
            ->where('userID', $this->userId)
            ->where('flag', 1)
            ->where('palet', $this->po);

        // remove data in abnormal_materials
        $dd = abnormalMaterial::where(['pallet_no' => $this->paletCode, 'kit_no' => $this->po,])->delete();

        $loopData = $fixProduct->get();
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
                        abnormalMaterial::create([
                            'kit_no' => $this->po,
                            'surat_jalan' => $this->surat_jalan,
                            'pallet_no' => $this->paletCode,
                            'material_no' => $data->material,
                            'picking_qty' => $value,
                            'locate' => $data->location_cd,
                            'trucking_id' => $data->trucking_id,
                            'user_id' => $this->userId,
                            'status' => 1,
                            'line_c' => $data->line_c,
                            'locate' => $prop_ori['location'] ?? null,
                            'setup_by' => $prop_ori['setup_by'],
                        ]);
                    }
                    $masuk++;
                }
                // kurang
                if ($data->total > $data->counter) {
                    $count = $data->pax - $masuk;
                    $kurangnya = $data->total - $data->counter;
                    abnormalMaterial::create([
                        'pallet_no' => $this->paletCode,
                        'kit_no' => $this->po,
                        'surat_jalan' => $this->surat_jalan,
                        'material_no' => $data->material,
                        'picking_qty' => $kurangnya,
                        'locate' => $data->location_cd,
                        'trucking_id' => $data->trucking_id,
                        'user_id' => $this->userId,
                        'status' => 0,
                        'line_c' => $data->line_c,
                        'locate' => $prop_ori['location'] ?? null,
                        'setup_by' => $prop_ori['setup_by'],
                    ]);
                }
            } else {
                $sisa = $data->sisa;
                for ($i = 1; $i <= $data->pax; $i++) {
                    $qty = floor($data->sisa / $data->pax);
                    $sisa = $sisa - $qty;
                    if ($i > $data->pax) $qty = $sisa;
                    # code...
                    abnormalMaterial::create([
                        'kit_no' => $this->po,
                        'surat_jalan' => $this->surat_jalan,
                        'pallet_no' => $this->paletCode,
                        'material_no' => $data->material,
                        'picking_qty' => $qty,
                        'locate' => $data->location_cd,
                        'trucking_id' => $data->trucking_id,
                        'user_id' => $this->userId,
                        'status' => 0,
                        'line_c' => $data->line_c,
                        'locate' => $prop_ori['location'] ?? null,
                        'setup_by' => $prop_ori['setup_by'],
                    ]);
                }
            }
        }
        $this->resetPage();
    }

    public function resetPage()
    {
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
        DB::table('temp_counters')->where('userID', $this->userId)->where('flag', 1)->delete();
        $this->dispatch('SJFocus');
    }

    public function render()
    {
        $this->paletCode = $this->palet . "-" . $this->noPalet;

        $getScanned = DB::table('material_in_stock')->select('material_no')
            ->where('pallet_no', $this->paletCode)
            ->union(DB::table('abnormal_materials')->select('material_no')->where('pallet_no', $this->po))
            ->pluck('material_no')
            ->all();

        $productsQuery = DB::table('material_setup_mst_supplier as a')->where('a.kit_no', $this->po)
            ->selectRaw('a.material_no,a.picking_qty,count(a.picking_qty) as pax,a.kit_no,b.picking_qty as stock_in,a.line_c,a.setup_by')
            ->leftJoin('material_in_stock as b', function ($join) {
                $join->on('a.material_no', '=', 'b.material_no')
                    ->on('a.kit_no', '=', 'b.kit_no')
                    ->where('b.pallet_no', $this->paletCode);
            })
            ->groupBy(['a.material_no', 'a.kit_no', 'a.line_c', 'a.setup_by', 'a.picking_qty', 'b.picking_qty'])
            ->orderBy('a.material_no')
            ->orderByDesc('a.line_c');


        $getall = $productsQuery->get();
        $materialNos = $getall->pluck('material_no')->all();

        $getTempCounterData = DB::table('temp_counters')
            ->select(['material', 'line_c'])
            ->where('palet', $this->po)
            ->whereIn('material', $materialNos);
        $existingMaterial = $getTempCounterData->pluck('material')->all();
        $existingLine = $getTempCounterData->pluck('line_c')->all();
        $loopKe = 1;
        foreach ($getall as $value) {
            if ($loopKe == 1) {
                $this->input_setup_by = $value->setup_by;
            }
            $loopKe++;

            $materialExists = in_array($value->material_no, $existingMaterial);
            $lineExists = in_array($value->line_c, $existingLine);
            if (!$materialExists || !$lineExists) {

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
        }


        $scannedCounter = DB::table('temp_counters as a')
            ->leftJoin('material_mst as b', 'a.material', '=', 'b.matl_no')
            ->where('palet', $this->po)
            ->select('a.*', 'b.loc_cd as location_cd')
            ->where('userID', $this->userId)
            ->orderBy('material')
            ->orderByDesc('line_c')
            ->get();


        $props = [0, 'No Data'];
        if ($getall->count() == 0 && count($getScanned) > 0) {
            $props = [1, 'Scan Confirmed'];
        }
        $this->dispatch('paletFocus');

        $this->listMaterial = $getall;
        $this->listMaterialScan = $scannedCounter;

        return view('livewire.purchase-order-in');
    }
}
