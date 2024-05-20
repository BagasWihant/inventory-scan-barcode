<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ListProduct extends Component
{
    use WithPagination;
    public $products, $search,$changes=false;

    public function mount($products){
        $this->products = $products;
    }
    public function render()
    {
        
        $this->changes = false;
        if ($this->search) {
            DB::table('products')->where('product_barcode', '=', $this->search)->update(['status' => '1','updated_at'=>Carbon::now()]);
            $this->search = null;
            $this->changes = true;
        }

        // PRODUK BELUM DISCAN
        $dataProducts = DB::table('products')
            ->where('pallet_barcode', '=', $this->products)
            ->where('status', '0')->paginate(5, pageName: 'product');

        // PRODUK telah DI SCAN
        $productScanned = DB::table('products')
            ->where('pallet_barcode', '=', $this->products)
            ->where('status', '1')->orderByDesc('updated_at')->paginate(5,pageName: 'productScanned');


        return view('livewire.list-product', ['dataproducts' => $dataProducts, 'productScanned' => $productScanned,'changes'=>$this->changes]);
    }
}
