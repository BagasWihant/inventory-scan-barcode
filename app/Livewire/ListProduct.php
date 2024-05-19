<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ListProduct extends Component
{
    use WithPagination;
    public $products, $search, $productScanned, $dataProducts;
    public function render()
    {
        
        if ($this->search) {
            DB::table('products')->where('product_barcode', '=', $this->search)->update(['status' => '1']);
            $this->search = null;
        }

        // PRODUK BELUM DISCAN
        $this->dataProducts = DB::table('products')
            ->where('pallet_barcode', '=', $this->products)
            ->where('status', '0')->get();

        // PRODUK telah DI SCAN
        $this->productScanned = DB::table('products')
            ->where('pallet_barcode', '=', $this->products)
            ->where('status', '1')->get();


        return view('livewire.list-product', ['product' => $this->dataProducts, 'productScanned' => $this->productScanned]);
    }
}
