<?php

namespace App\Livewire;

use App\Models\itemIn;
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
        $getScanned = DB::table('item_ins')->where('pallet_no', $this->paletBarcode)->select('material_no')->groupBy('material_no')->pluck('material_no')->all();
        $productsQuery = DB::table('products as p')
            ->selectRaw('p.pallet_no, p.material_no, count(p.material_no) as pax, sum(p.picking_qty) as picking_qty')
            ->where('p.pallet_no', $this->paletBarcode)
            ->whereNotIn('material_no',$getScanned)
            ->groupBy('p.pallet_no', 'p.material_no');

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

    public function resetPage(){
        $this->paletBarcode = null;
        $this->produkBarcode = null;
        DB::table('temp_counters')->where('userID', $this->userId)->delete();
    }

    public function confirm()
    {
        $fixProduct = DB::table('temp_counters')
            ->where('palet', $this->paletBarcode)->where('sisa', 0)->whereRaw('total = counter')
            ->pluck('material')
            ->all();

        DB::table('products')->select('material_no', 'picking_qty')->where('pallet_no', $this->paletBarcode)->whereIn('material_no', $fixProduct)->orderBy('id')->chunk(10, function (Collection $data) {
            foreach ($data as $value) {
                itemIn::create([
                    'pallet_no' => $this->paletBarcode,
                    'material_no' => $value->material_no,
                    'picking_qty' => $value->picking_qty
                ]);
            }
        });

        $this->resetPage();
    }
}
