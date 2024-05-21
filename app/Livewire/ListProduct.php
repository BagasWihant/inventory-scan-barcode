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
    public $userId, $products, $produkBarcode, $paletBarcode, $changes = false;

    public $scannedCounter = [];

    public function mount()
    {
        $this->userId = auth()->user()->id;
        DB::table('temp_counters')->where('userID', $this->userId)->delete();
    }

    public function productBarcodeScan()
    {
        if (strlen($this->produkBarcode) > 2) {
            $tempCount = DB::table('temp_counters')->where('id', $this->produkBarcode)->where('userID', $this->userId);
            $productDetail = DB::table('products')->where('material_no',$this->produkBarcode)->where('pallet_barcode',$this->paletBarcode)->first();
            $get = $tempCount->first();
            $counter = $get->counter+ $productDetail->qty;
            $sisa = $get->sisa - $productDetail->qty;
            $tempCount->update(['counter'=>$counter,'sisa'=>$sisa]);
        }
        $this->produkBarcode = null;
    }

    public function render()
    {
        $this->changes = false;
        if ($this->paletBarcode) {
            $paletUpdate = DB::table('pallets')->where('pallet_barcode',  $this->paletBarcode);

            if (($paletUpdate->count()) > 0) {
                $paletUpdate->update([
                    'scanned_by' => $this->userId
                ]);
            }
            $this->dispatch('produkFocus');

            $this->changes = true;
        }

        $productsQuery = DB::table('products')
            ->selectRaw('pallet_barcode, material_no, count(material_no) as pax, sum(qty) as qty')
            ->where('pallet_barcode', $this->paletBarcode)
            ->groupBy('pallet_barcode', 'material_no');
        $getall = $productsQuery->get();
        $productsInPalet = $productsQuery->paginate(5, ['*'], 'product');

        foreach ($getall as $value) {
            $counterExists = DB::table('temp_counters')->where('id', $value->material_no)->exists();
            if (!$counterExists) {
                tempCounter::create([
                    'id' => $value->material_no,
                    'palet' => $this->paletBarcode,
                    'userID' => $this->userId,
                    'sisa' => $value->qty,
                    'total' => $value->qty,
                    'pax' => $value->pax,
                ]);
            }
        }
        

        $scannedCounter = DB::table('temp_counters')->where('palet', $this->paletBarcode)->where('userID', $this->userId)->paginate(5, ['*'], 'count');

        return view('livewire.list-product', [
            'productsInPalet' => $productsInPalet,
            'scanned' => $scannedCounter,
            'changes' => $this->changes
        ]);
    }
}
