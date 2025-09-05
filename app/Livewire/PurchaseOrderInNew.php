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

class PurchaseOrderInNew extends Component
{
    use WithPagination;
    public $po;
    public $input_setup_by;
    public $sws_code;
    public $line_code;
    public $userId;

    public function mount()
    {
        $this->userId = auth()->user()->id;
    }
    public function searchDropdown($key)
    {
        return DB::table('material_setup_mst_supplier')
            ->selectRaw('kit_no, kit_no as id')
            ->where('kit_no', 'like', "%$key%")
            ->groupBy('kit_no')
            ->limit(10)
            ->get();
    }

    public function selectDropdown($item)
    {
        $this->po = $item['id'];

        $this->dispatch(
            'po-selected',
            po: $this->po
        );
    }

    public function scanMaterial($data)
    {
        // dd($data);
        $supplierCode = DB::table('material_conversion_mst')->where('supplier_code', $data['material_no'])->select('sws_code')->first();
        if ($supplierCode) {
            $this->sws_code = $supplierCode->sws_code;
        }

        $getSetupby = DB::table('material_setup_mst_supplier')->select('setup_by')->where('kit_no', $this->po)->first();
        if ($getSetupby) $this->input_setup_by = $getSetupby->setup_by;

        if ($this->input_setup_by == "PO COT" && DB::table('WH_rcv_QRHistory')->where('QR', $data['qr'])->where('user_id', $this->userId)->where('status', 1)->exists()) {
             return $this->dispatch('alert', ['title' => 'Warning', 'time' => 4000, 'icon' => 'warning', 'text' => "QR sudah pernah discan"]);
        }

        // PCL-L24-1607 / S0200381510010150          2108P6002  / yd30  / 210824 / 2 / ANDIK
        $this->line_code = $data['line'];

        DB::table('WH_rcv_QRHistory')->insert([
             'QR' => $data['qr'],
             'user_id' => $this->userId,
             'PO' => $this->po,
             'line_code' => $this->line_code,
             'material_no' => $this->sws_code,
             'status' => 0,
             'created_at' => date('Y-m-d H:i:s')
        ]);

        $joinCondition = function ($join) {
            $join->on('a.material_no', '=', 'b.material_no')
                ->on('a.kit_no', '=', 'b.kit_no');
            // ->where('b.pallet_no', $paletCode);
        };
        $groupByColumns = ['a.material_no', 'a.kit_no', 'a.line_c', 'a.setup_by', 'a.picking_qty',  'scanned_time', 'm.loc_cd', 'd.trucking_id'];

        if ($this->input_setup_by == "PO COT") {
            $joinCondition = function ($join) {
                $join->on('a.material_no', '=', 'b.material_no')
                    ->on('a.kit_no', '=', 'b.kit_no')
                    ->on('a.line_c', '=', 'b.line_c');
                // ->where('b.pallet_no', $paletCode);
            };
        }

        $productsQuery = DB::table('material_setup_mst_supplier as a')
            // ->whereIn('a.material_no', $material_no_list)
            ->where('a.kit_no', $this->po)
            ->where('a.line_c', $this->line_code)

            ->selectRaw('
                    a.material_no,
                    a.picking_qty,
                    count(a.picking_qty) as pax,
                    a.kit_no, sum(b.picking_qty) as stock_in,
                    a.line_c,
                    a.setup_by,
                    m.loc_cd as location_cd_ori,
                    d.trucking_id')
            ->leftJoin('material_in_stock as b', $joinCondition)
            ->leftJoin('material_mst as m', 'a.material_no', '=', 'm.matl_no')
            ->leftJoin('delivery_mst as d', 'a.kit_no', '=', 'd.pallet_no')
            ->groupBy($groupByColumns)
            ->orderByDesc('scanned_time')
            ->where('a.line_c', $this->line_code)

            ->orderBy('a.material_no');
        $value = $productsQuery->get();

        foreach ($value as $k) {
            $k->total = $k->stock_in > 0 ? $k->picking_qty - $k->stock_in : $k->picking_qty;
            if ($data['material_no'] == $k->material_no && $data['line'] == $k->line_c) {
                $k->counter = (int) $data['qty'];
                $k->location_cd = $data['location_cd'];
                $k->scanned = [[(int)$data['qty'], 1]];
            } else {
                $k->counter = 0;
                $k->location_cd = $k->location_cd_ori;
                $k->scanned = [];
            }
        }

        return $value;
    }

    public function confirm($req)
    {
        $surat_jalan = $req['sj'];
        $scanned = $req['scanned'];


        // GENERATE PALET CODE
        $getConfig = DB::table('WH_config')->select('value')->whereIn('config', ['PalletCodeInStock', 'PeriodInStock'])->get();
        $ym = date('ym');

        $PalletCodeInStock = (int)$getConfig[0]->value + 1;
        if ($getConfig[1]->value != $ym) {
            $PalletCodeInStock = 1;
            DB::table('WH_config')->where('config', 'PeriodInStock')->update(['value' => $ym]);
        }


        $generatePaletCode = str_pad($PalletCodeInStock, 4, '0', STR_PAD_LEFT);
        $paletCode = 'L-' . $ym . '-' . $generatePaletCode;

        DB::table('WH_config')->where('config', 'PalletCodeInStock')->update(['value' => $PalletCodeInStock]);
        foreach ($scanned as $data) {

            if (!empty($data['scanned'])) {
                $scanData = $data['scanned'];
                $totalScanPerMaterial = count($scanData);
                $iteration = 1;
                $kelebihan = $data['counter'] - abs($data['total']);
                $sisaTerakhir = 0;
                foreach ($scanData as $value) {
                    $qtyPerScan = $value[0];
                    $box = $value[1];

                    if (($iteration == $totalScanPerMaterial) && ($kelebihan > 0 || $data['total'] < 0)) {
                        $picking_qty = $data['total'] < 0 ? $qtyPerScan : $kelebihan;
                        abnormalMaterial::create([
                            'kit_no' => $this->po,
                            'surat_jalan' => $surat_jalan,
                            'pallet_no' => $paletCode,
                            'material_no' => $data['material_no'],
                            'picking_qty' => $picking_qty,
                            'trucking_id' => $data['trucking_id'],
                            'user_id' => $this->userId,
                            'status' => 1,
                            'box' => $box,
                            'line_c' => $data['line_c'],
                            'setup_by' => $data['setup_by'],
                            'locate' => $this->input_setup_by == 'PO MCS' ? $data['location_cd_ori'] : $data['location_cd'],
                        ]);
                        $sisaTerakhir = $qtyPerScan - $picking_qty;
                        if ($sisaTerakhir > 0) {
                            itemIn::create([
                                'pallet_no' => $paletCode,
                                'material_no' => $data['material_no'],
                                'picking_qty' => $sisaTerakhir,
                                'locate' => $this->input_setup_by == 'PO MCS' ? $data['location_cd_ori'] : $data['location_cd'],
                                'trucking_id' => $data['trucking_id'],
                                'kit_no' => $this->po,
                                'surat_jalan' => $surat_jalan,
                                'user_id' => $this->userId,
                                'line_c' => $data['line_c'],
                                'box' => $box,
                                'setup_by' => $data['setup_by'],
                            ]);
                        }
                    } else {
                        itemIn::create([
                            'pallet_no' => $paletCode,
                            'material_no' => $data['material_no'],
                            'picking_qty' => $value,
                            'locate' => $this->input_setup_by == 'PO MCS' ? $data['location_cd_ori'] : $data['location_cd'],
                            'trucking_id' => $data['trucking_id'],
                            'kit_no' => $this->po,
                            'surat_jalan' => $surat_jalan,
                            'user_id' => $this->userId,
                            'line_c' => $data['line_c'],
                            'box' => $box,
                            'setup_by' => $data['setup_by'],
                        ]);
                    }
                    $iteration++;
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.purchase-order-in-new');
    }
}
