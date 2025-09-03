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
        $getSetupby = DB::table('material_setup_mst_supplier')->select('setup_by')->where('kit_no', $this->po)->first();
        if ($getSetupby) $this->input_setup_by = $getSetupby->setup_by;

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

        // if ($this->input_setup_by == "PO COT" && DB::table('WH_rcv_QRHistory')->where('QR', $data['qr'])->where('user_id', $this->userId)->where('status', 1)->exists()) {
        //     return $this->dispatch('alert', ['title' => 'Warning', 'time' => 4000, 'icon' => 'warning', 'text' => "QR sudah pernah discan"]);
        // }

        // PCL-L24-1607 / S0200381510010150          2108P6002  / yd30  / 210824 / 2 / ANDIK
        $this->line_code = $data['line'];

        // DB::table('WH_rcv_QRHistory')->insert([
        //     'QR' => $data['qr'],
        //     'user_id' => $this->userId,
        //     'PO' => $this->po,
        //     'line_code' => $this->line_code,
        //     'material_no' => $this->sws_code,
        //     'status' => 0,
        //     'created_at' => date('Y-m-d H:i:s')
        // ]);

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

        $productsQuery = DB::table('material_setup_mst_supplier as a')
            ->where('a.kit_no', $this->po)
            ->selectRaw('a.material_no, a.picking_qty, count(a.picking_qty) as pax, a.kit_no, sum(b.picking_qty) as stock_in, a.line_c, a.setup_by')
            ->leftJoin('material_in_stock as b', $joinCondition)
            ->groupBy($groupByColumns)
            ->orderByDesc('scanned_time')
            ->where('a.line_c', $this->line_code)
            ->orderBy('a.material_no');

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
                    a.setup_by')
            ->leftJoin('material_in_stock as b', $joinCondition)
            ->groupBy($groupByColumns)
            ->orderByDesc('scanned_time')
            ->orderBy('a.material_no');
        $value = $productsQuery->get();

        foreach ($value as $k) {
            $k->total = $k->stock_in > 0 ? $k->picking_qty - $k->stock_in : $k->picking_qty;
            if ($data['material_no'] == $k->material_no && $data['line'] == $k->line_c) {
                $k->counter = (int) $data['qty'];
            } else {
                $k->counter = 0;
            }
        }

        return $value;
    }


    public function render()
    {
        return view('livewire.purchase-order-in-new');
    }
}
