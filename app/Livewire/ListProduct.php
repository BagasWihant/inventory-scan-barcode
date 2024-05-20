<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ListProduct extends Component
{
    use WithPagination;
    public $products, $produkBarcode, $paletBarcode, $changes = false;

    public function render()
    {

        $this->changes = false;
        if ($this->paletBarcode) {
            $paletUpdate = DB::table('pallets')->where('pallet_barcode',  $this->paletBarcode);

            if (($paletUpdate->count()) > 0) {
                $paletUpdate->update([
                    'scanned_by' => auth()->user()->id
                ]);
            }
            $this->dispatch('produkFocus');

            $this->changes = true;
        }

        // PRODUK BELUM DISCAN
        $productsInPalet = DB::table('products')->select(DB::raw('pallet_barcode,material_no, count(material_no) as pax'))
            ->where('pallet_barcode', $this->paletBarcode)
            ->groupBy('pallet_barcode', 'material_no')
            ->paginate(5, pageName: 'product');

        // PRODUK telah DI SCAN
        // $productScanned = DB::table('products')
        //     ->where('pallet_barcode', $this->products)
        //     ->where('status', '1')->orderByDesc('updated_at')->paginate(5, pageName: 'productScanned');


        return view('livewire.list-product', ['productsInPalet' => $productsInPalet,  'changes' => $this->changes]);
    }
}
