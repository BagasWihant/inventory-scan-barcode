<?php

namespace App\Livewire;

use App\Models\itemIn;
use App\Models\itemSisa;
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
    public $userId, $products, $produkBarcode, $paletBarcode, $changes = false, $previousPaletBarcode;

    public $scannedCounter = [];

    public function mount()
    {
        $this->userId = auth()->user()->id;
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
        if (strlen($this->produkBarcode) > 2) {
            $supplierCode = DB::table('material_conversion_mst')->where('supplier_code', $this->produkBarcode)->select('sws_code')->first();
            if ($supplierCode) {
                $tempCount = DB::table('temp_counters')->where('material', $supplierCode->sws_code)->where('userID', $this->userId)->where('palet', $this->paletBarcode);
                $data = $tempCount->first();
                if ($tempCount->count() > 0) {
                    if ($data->total == $data->counter && $data->sisa == 0) {
                        $this->dispatch('cannotScan');
                        $this->produkBarcode = null;
                        return;
                    }


                    $productDetail = DB::table('material_setup_mst_CNC_KIAS2')->select('picking_qty')->where('material_no', $supplierCode->sws_code)->where('pallet_no', $this->paletBarcode)->first();
                    $counter = $data->counter + $productDetail->picking_qty;
                    $sisa = $data->sisa - $productDetail->picking_qty;
                    $tempCount->update(['counter' => $counter, 'sisa' => $sisa]);
                }
            }
        }
        $this->produkBarcode = null;
    }

    public function render()
    {
        $getScanned = DB::table('material_in_stock')->where('pallet_no', $this->paletBarcode)->select('material_no')->groupBy('material_no')->pluck('material_no')->all();
        $productsQuery = DB::table('material_setup_mst_CNC_KIAS2')
            ->selectRaw('pallet_no, material_no, count(material_no) as pax, sum(picking_qty) as picking_qty')
            ->where('pallet_no', $this->paletBarcode)
            ->whereNotIn('material_no', $getScanned)
            ->groupBy('pallet_no', 'material_no')->orderByDesc('material_no');

        $getall = $productsQuery->get();

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

        // KURANG
        $kurang = DB::table('material_kurang')
            ->selectRaw('pallet_no, material_no, count(material_no) as pax, sum(picking_qty) as picking_qty')
            ->where('pallet_no', $this->paletBarcode)
            ->groupBy('pallet_no', 'material_no')->orderByDesc('material_no')->get();

        foreach ($kurang as $value) {
            $getall->push($value);
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

        $scannedCounter = DB::table('temp_counters')->where('palet', $this->paletBarcode)->where('userID', $this->userId)->orderByDesc('material')->get();
        
        return view('livewire.list-product', [
            'productsInPalet' => $getall,
            'scanned' => $scannedCounter,
            'changes' => $this->changes
        ]);
    }

    public function resetPage()
    {
        $this->paletBarcode = null;
        $this->produkBarcode = null;
        DB::table('temp_counters')->where('userID', $this->userId)->delete();
        $this->dispatch('paletFocus');
    }

    public function confirm()
    {
        $fixProduct = DB::table('temp_counters')->where('palet', $this->paletBarcode);
        $b = $fixProduct->get();

        foreach ($b as $data) {
            if ($data->total == $data->counter && $data->sisa == 0) {
                $pax = $data->pax;
                $qty = $data->total / $pax;
                for ($i = 1; $i <= $pax; $i++) {
                    itemIn::create([
                        'pallet_no' => $this->paletBarcode,
                        'material_no' => $data->material,
                        'picking_qty' => $qty
                    ]);
                }
                
            } elseif ($data->counter > 0 && $data->sisa !== 0) {
                $qty = $data->total / $data->pax;
                $jmlIn = $data->counter / $qty;
                for ($i = 0; $i < $jmlIn; $i++) {
                    itemIn::create([
                        'pallet_no' => $this->paletBarcode,
                        'material_no' => $data->material,
                        'picking_qty' => $qty
                    ]);
                }

                $jmlSisa = ($data->total / $qty) - $jmlIn;
                for ($i = 0; $i < $jmlSisa; $i++) {
                    itemSisa::create([
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
