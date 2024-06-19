<?php

namespace App\Livewire;

use App\Models\itemIn;
use Livewire\Component;

class PrintStockTaking extends Component
{
    public function render()
    {
        $list = itemIn::where('user_id', auth()->user()->id)->get();
        return view('livewire.print-stock-taking',['listData'=>$list]);
    }
}
