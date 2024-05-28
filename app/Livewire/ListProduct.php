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
        // $tes = "P104101G0B0-L290324ES07008-Q1500 // 1H030C.GX-BX290324ES07008-[0] //10036|AVSS 0.3 G-B|1500|13E290324ES07008";
        // $tes = "P104101G0B0-L290324ES07008-Q1500 // 1H030C.GX-BX290324ES07008-[0] //10036|AVSS 0.3 L|1500|13E290324ES07008";
        // $tes = "P104101G0B0-L290324ES07008-Q1500 // 1H030C.GX-BX290324ES07008-[0] //10036|AVSS 0.85 G|1500|13E290324ES07008";
        $produkBarcode = explode('|', $this->produkBarcode);
        if (isset($produkBarcode[1])) {

            $titik = explode(".", $produkBarcode[1]);
            $kurang = explode("-", $titik[1]);
            $spasi = explode(" ", $kurang[0]);
            if(strlen($spasi[0]) >1) $spasi[0] = $spasi[0];
            if(strlen($spasi[0]) ==1) $spasi[0] = "$spasi[0]0";

            if(count($kurang) >1) $text = "$titik[0]$spasi[0]$spasi[1]-$kurang[1]";
            else $text = "$titik[0]$spasi[0]$spasi[1]";
            
            $this->produkBarcode = str_replace(" ", '', $text);

            if (strlen($this->produkBarcode) > 2) {
                $tempCount = DB::table('temp_counters')->where('material_fix', $this->produkBarcode)->where('userID', $this->userId)->where('palet', $this->paletBarcode);
                $data = $tempCount->first();
                if ($tempCount->count() > 0) {
                    if ($data->total == $data->counter && $data->sisa == 0) {
                        $this->dispatch('cannotScan');
                        $this->produkBarcode = null;
                        return;
                    }
                    $get = $tempCount->first();
                    $productDetail = DB::table('products')->where('material_no', $get->material)->where('pallet_no', $this->paletBarcode)->first();
                    $counter = $get->counter + $productDetail->picking_qty;
                    $sisa = $get->sisa - $productDetail->picking_qty;
                    $tempCount->update(['counter' => $counter, 'sisa' => $sisa]);
                }
            }
        }
        $this->produkBarcode = null;
    }

    public function render()
    {
        $getScanned = DB::table('material_in_stock')->where('pallet_no', $this->paletBarcode)->select('material_no')->groupBy('material_no')->pluck('material_no')->all();
        $productsQuery = DB::table('products as p')
            ->selectRaw('p.pallet_no, p.material_no, count(p.material_no) as pax, sum(p.picking_qty) as picking_qty,replace(material_no," ","") as fix_material_no')
            ->where('p.pallet_no', $this->paletBarcode)
            ->whereNotIn('material_no', $getScanned)
            ->groupBy('p.pallet_no', 'p.material_no')->orderByDesc('material_no');

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
                        'material_fix' => $value->fix_material_no,
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


        $scannedCounter = DB::table('temp_counters')->where('palet', $this->paletBarcode)->where('userID', $this->userId)->orderByDesc('material')->get();
        // $productsInPalet =

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
        $collectCounter = collect($b);

        // DB::table('products')->select('material_no', 'picking_qty')->where('pallet_no', $this->paletBarcode)->orderBy('id')->chunk(10, function (Collection $data) {
        //     foreach ($data as $value) {
        //     }
        // });
        foreach ($collectCounter as $data) {
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
