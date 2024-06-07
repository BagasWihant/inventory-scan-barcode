<?php

namespace App\Livewire;

use App\Models\itemIn;
use App\Models\itemKurang;
use App\Models\MaterialKelebihan;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\tempCounter;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ListProduct extends Component
{
    use WithPagination;
    public $userId, $products, $produkBarcode, $paletBarcode, $changes = false, $previousPaletBarcode, $sws_code, $qtyPerPax;

    public $scannedCounter = [];

    public function mount()
    {
        $this->userId = auth()->user()->id;
    }

    #[On('insertNew')]
    public function insertNew($qty = 0, $save = true)
    {
        if ($save) {
            tempCounter::create([
                'material' => $this->sws_code,
                'palet' => $this->paletBarcode,
                'userID' => $this->userId,
                'sisa' => "-$qty",
                'total' => $qty,
                'counter' => $qty,
                'pax' => 1,
                'qty_more' => 1,
            ]);

            DB::table('material_setup_mst_cnc_kias2')->insert([
                'pallet_no' => $this->paletBarcode,
                'serial_no' => '00000',
                "material_no" => $this->sws_code,
                'picking_qty' => $qty,
                'line_c'=>'NewItem',
                'setup_by'=>'dev',
                'setup_date' => Carbon::now(),
            ]);
        }
        $this->produkBarcode = null;
    }

    public function paletBarcodeScan()
    {
        $this->paletBarcode = substr($this->paletBarcode, 0, 10);
        $this->changes = false;
        if (strlen($this->paletBarcode) > 2  && $this->paletBarcode !== $this->previousPaletBarcode) {
            DB::table('temp_counters')->where('userID', $this->userId)->delete();

            $this->dispatch('produkFocus');

            $this->changes = true;
            $this->previousPaletBarcode = $this->paletBarcode;
        }
    }

    public function productBarcodeScan()
    {
        if ($this->products->count() == 0) {
            $this->produkBarcode = null;
            return;
        } 
        
        if (strlen($this->produkBarcode) > 2) {
            $supplierCode = DB::table('material_conversion_mst')->where('supplier_code', $this->produkBarcode)->select('sws_code')->first();
            if ($supplierCode) {
                $this->sws_code = $supplierCode->sws_code;

                $tempCount = DB::table('temp_counters')
                    ->where('material', $supplierCode->sws_code)
                    ->where('userID', $this->userId)
                    ->where('palet', $this->paletBarcode);
                $data = $tempCount->first();

                if ($tempCount->count() > 0) {

                    $qry = DB::table('material_setup_mst_CNC_KIAS2')->select('picking_qty', 'serial_no')->where('material_no', $supplierCode->sws_code)->where('pallet_no', $this->paletBarcode);
                    if ($qry->count() > 0) {

                        $productDetail = $qry->first();
                        $counter = $data->counter + $productDetail->picking_qty;
                        $sisa = $data->sisa - $productDetail->picking_qty;

                        if ($data->total < $data->counter || $data->sisa <= 0) {

                            $this->produkBarcode = null;
                            $more = $data->qty_more + 1;
                            $tempCount->update(['counter' => $counter, 'sisa' => $sisa, 'qty_more' => $more]);
                            return;
                        }

                        $tempCount->update(['counter' => $counter, 'sisa' => $sisa]);
                    }
                } else {
                    // insert barcode tidak terecord
                    $this->dispatch('newItem');
                }
            }
        }
        $this->produkBarcode = null;
    }

    public function render()
    {
        // $this->paletBarcode = 'Y-01-00003';
        $getScanned = DB::table('material_in_stock')->select('material_no')
            ->where('pallet_no', $this->paletBarcode)
            ->union(DB::table('material_kelebihans')->select('material_no')->where('pallet_no', $this->paletBarcode))
            ->union(DB::table('material_kurang')->select('material_no')->where('pallet_no', $this->paletBarcode))
            ->pluck('material_no')
            ->all();

        $productsQuery = DB::table('material_setup_mst_CNC_KIAS2')
            ->selectRaw('pallet_no, material_no, count(material_no) as pax, sum(picking_qty) as picking_qty, min(serial_no) as serial_no')
            ->where('pallet_no', $this->paletBarcode)
            ->whereNotIn('material_no', $getScanned)
            ->groupBy('pallet_no', 'material_no')
            ->orderByDesc('picking_qty')
            ->orderByDesc('material_no');

        $getall = $productsQuery->get();
        $this->products = $getall;
        $materialNos = $getall->pluck('material_no')->all();

        $existingCounters = DB::table('temp_counters')
            ->where('palet', $this->paletBarcode)
            ->whereIn('material', $materialNos)
            ->pluck('material')
            ->all();

        foreach ($getall as $value) {

            $counterExists = in_array($value->material_no, $existingCounters);
            if (!$counterExists) {
                try {
                    DB::beginTransaction();
                    tempCounter::create([
                        'material' => $value->material_no,
                        'palet' => $this->paletBarcode,
                        'userID' => $this->userId,
                        'sisa' => $value->picking_qty,
                        'total' => $value->picking_qty,
                        'pax' => $value->pax,
                    ]);
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                }
            }
        }


        $scannedCounter = DB::table('temp_counters')->where('palet', $this->paletBarcode)
        ->where('userID', $this->userId)
        ->orderByDesc('total')
        ->orderByDesc('material')->get();

        return view('livewire.list-product', [
            'productsInPalet' => $getall,
            'scanned' => $scannedCounter,
            'changes' => $this->changes
        ]);
    }

    public function resetPage()
    {
        DB::table('temp_counters')->where('userID', $this->userId)->delete();
        
        DB::table('material_setup_mst_cnc_kias2')
        ->where('serial_no', '00000')
        ->where('pallet_no', $this->paletBarcode)
        ->where('line_c', 'NewItem')->delete();
        
        $this->paletBarcode = null;
        $this->produkBarcode = null;
        $this->dispatch('paletFocus');
    }

    public function confirm()
    {
        $fixProduct = DB::table('temp_counters')
            ->where('userID', $this->userId)
            ->where('palet', $this->paletBarcode);
        $b = $fixProduct->get();

        foreach ($b as $data) {
            $pax = $data->pax;
            $qty = $data->total / $pax;
            $kelebihan = $data->qty_more;
            if ($data->counter > 0) {
                // insrt kelebihan
                if ($kelebihan > 0) {

                    for ($i = 1; $i <= $kelebihan; $i++) {
                        MaterialKelebihan::create([
                            'pallet_no' => $this->paletBarcode,
                            'material_no' => $data->material,
                            'picking_qty' => $qty,
                        ]);
                    }
                }

                $jmlIn = $data->counter / $qty;
                $jmlIn = $jmlIn - $kelebihan;
                for ($i = 0; $i < $jmlIn; $i++) {
                    itemIn::create([
                        'pallet_no' => $this->paletBarcode,
                        'material_no' => $data->material,
                        'picking_qty' => $qty
                    ]);
                }
                if ($kelebihan == 0) {

                    $jmlSisa = $data->pax - $jmlIn;
                    for ($i = 0; $i < $jmlSisa; $i++) {
                        itemKurang::create([
                            'pallet_no' => $this->paletBarcode,
                            'material_no' => $data->material,
                            'picking_qty' => $qty
                        ]);
                    }
                }
            } else {
                for ($i = 0; $i < $pax; $i++) {
                    itemKurang::create([
                        'pallet_no' => $this->paletBarcode,
                        'material_no' => $data->material,
                        'picking_qty' => $qty
                    ]);
                }
            }
        }
        $this->resetPage();
    }
}
