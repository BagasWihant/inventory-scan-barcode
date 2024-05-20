<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ListProduct extends Component
{
    use WithPagination;
    public $products, $search;

    public function mount($products){
        $this->products = $products;
    }
    public function render()
    {
        
        if ($this->search) {
            DB::table('products')->where('product_barcode', '=', $this->search)->update(['status' => '1']);
            $this->search = null;
        }

        // PRODUK BELUM DISCAN
        $dataProducts = DB::table('products')
            ->where('pallet_barcode', '=', $this->products)
            ->where('status', '0')->paginate(5, pageName: 'product');

        // PRODUK telah DI SCAN
        $productScanned = DB::table('products')
            ->where('pallet_barcode', '=', $this->products)
            ->where('status', '1')->paginate(5,pageName: 'productScanned');


        return view('livewire.list-product', ['dataproducts' => $dataProducts, 'productScanned' => $productScanned]);
    }
}
