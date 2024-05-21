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

            $paletUpdate = DB::table('pallets')->where('pallet_barcode',  $this->paletBarcode);

            if (($paletUpdate->count()) > 0) {
                $paletUpdate->update([
                    'scanned_by' => $this->userId
                ]);
            }
            $this->dispatch('produkFocus');

            $this->changes = true;
            $this->previousPaletBarcode = $this->paletBarcode;
        }
    }

    public function productBarcodeScan()
    {
        if (strlen($this->produkBarcode) > 2) {
            $tempCount = DB::table('temp_counters')->where('material', $this->produkBarcode)->where('userID', $this->userId);
            if ($tempCount->count() > 0) {

                $productDetail = DB::table('products')->where('material_no', $this->produkBarcode)->where('pallet_barcode', $this->paletBarcode)->first();
                $get = $tempCount->first();
                $counter = $get->counter + $productDetail->qty;
                $sisa = $get->sisa - $productDetail->qty;
                $tempCount->update(['counter' => $counter, 'sisa' => $sisa]);
            }
        }
        $this->produkBarcode = null;
    }

    public function render()
    {

        $productsQuery = DB::table('products')
            ->selectRaw('pallet_barcode, material_no, count(material_no) as pax, sum(qty) as qty')
            ->where('pallet_barcode', $this->paletBarcode)
            ->groupBy('pallet_barcode', 'material_no');
        // dd($productsQuery);
        $getall = $productsQuery->get();
        $productsInPalet = $productsQuery->paginate(5, ['*'], 'product');

        foreach ($getall as $value) {
            $counterExists = DB::table('temp_counters')->where('material', $value->material_no)->where('palet', $this->paletBarcode)->exists();
            if (!$counterExists) {
                tempCounter::create([
                    'material' => $value->material_no,
                    'palet' => $this->paletBarcode,
                    'userID' => $this->userId,
                    'sisa' => $value->qty,
                    'total' => $value->qty,
                    'pax' => $value->pax,
                ]);
            }
        }


        $scannedCounter = DB::table('temp_counters')->where('palet', $this->paletBarcode)->where('userID', $this->userId)
            // ->toRawSql();
            ->paginate(5, ['*'], 'count');
        // dd($scannedCounter);

        return view('livewire.list-product', [
            'productsInPalet' => $productsInPalet,
            'scanned' => $scannedCounter,
            'changes' => $this->changes
        ]);
    }
}
