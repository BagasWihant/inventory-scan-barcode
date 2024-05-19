<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ListProduct extends Component
{
    use WithPagination;
    public $products,$search;
    public function render()
    {
        $palets = null;
        if($this->search){
            // $palets= DB::table('products')->select(['product_name'])->where('product_barcode','=',$this->search)->get();
            DB::table('products')->where('product_barcode','=',$this->search)->update(['status' => '1']);
            $this->search = null;
        } 
        // dd($palets);
        $dataProducts = DB::table('products')->where('pallet_barcode','=',$this->products)->get();
        return view('livewire.list-product',compact('dataProducts','palets'));
    }
}
