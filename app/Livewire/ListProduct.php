<?php

namespace App\Livewire;

use App\Models\tempCounter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

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
            $tempCount = DB::table('temp_counters')->where('material', $this->produkBarcode)->where('userID', $this->userId)->where('palet', $this->paletBarcode);
            $data = $tempCount->first();
            if ($tempCount->count() > 0) {
                if ($data->total == $data->counter && $data->sisa == 0) {
                    $this->dispatch('cannotScan');
                    $this->produkBarcode = null;
                    return;
                }
                $productDetail = DB::table('products')->where('material_no', $this->produkBarcode)->where('pallet_no', $this->paletBarcode)->first();
                $get = $tempCount->first();
                $counter = $get->counter + $productDetail->picking_qty;
                $sisa = $get->sisa - $productDetail->picking_qty;
                $tempCount->update(['counter' => $counter, 'sisa' => $sisa]);
            }
        }
        $this->produkBarcode = null;
    }

    public function render()
    {

        $productsQuery = DB::table('products')
            ->selectRaw('pallet_no, material_no, count(material_no) as pax, sum(picking_qty) as picking_qty')
            ->where('pallet_no', $this->paletBarcode)
            ->groupBy('pallet_no', 'material_no');

        $getall = $productsQuery->get();
        $productsInPalet = $productsQuery->paginate(5, ['*'], 'product');

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


        $scannedCounter = DB::table('temp_counters')->where('palet', $this->paletBarcode)->where('userID', $this->userId)
            ->paginate(5, ['*'], 'count');

        return view('livewire.list-product', [
            'productsInPalet' => $productsInPalet,
            'scanned' => $scannedCounter,
            'changes' => $this->changes
        ]);
    }
}
